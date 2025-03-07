<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ciudad;
use App\Models\Sector;
use App\Models\Nacionalidad;

class PostsController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'comentario' => 'required',
            'id_blog' => 'required|exists:foro,id_blog',
            'is_fixed' => 'boolean'  // Agregar validación para is_fixed
        ]);

        try {
            $post = new Posts();
            $post->id_blog = $request->id_blog;
            $post->id_usuario = auth()->id();
            $post->comentario = $request->comentario;
            $post->is_fixed = $request->has('is_fixed');  // Manejar el checkbox
            $post->save();

            return redirect()->back()->with('success', 'Post creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el post');
        }
    }

    public function showPost($id_blog, $id)
    {
        // Primero incrementar el contador de visitas
        DB::table('posts')
            ->where('id', $id)
            ->increment('visitas');

        $ciudades = Ciudad::all();
        $sectores = Sector::all();
        $nacionalidades = Nacionalidad::all();

        $post = DB::table('posts as p')
            ->select('p.*', 'u.name as nombre_usuario', 'p.id_blog')
            ->leftJoin('users as u', 'p.id_usuario', '=', 'u.id')
            ->where('p.id', $id)
            ->first();

        if (!$post) {
            return redirect()->route('foro')->with('error', 'Post no encontrado');
        }

        $foro = DB::table('foro')
            ->where('id', $id_blog)
            ->first();

        // Modificamos la consulta de comentarios para incluir todos los datos del usuario necesarios
        $comentarios = DB::table('comentario as c')
            ->select(
                'c.*',
                'u.name as user_name',
                'u.foto as user_foto',
                'u.rol as user_rol'
            )
            ->leftJoin('users as u', 'c.id_usuario', '=', 'u.id')
            ->where('c.id_post', $id)
            ->orderBy('c.created_at', 'desc')
            ->get()
            ->map(function ($comentario) {
                // Creamos el objeto usuario con los datos necesarios
                $comentario->usuario = new \stdClass();
                $comentario->usuario->name = $comentario->user_name ?? 'Anónimo';
                $comentario->usuario->foto = $comentario->user_foto;
                $comentario->usuario->rol = $comentario->user_rol;

                // Eliminamos los campos temporales
                unset($comentario->user_name);
                unset($comentario->user_foto);
                unset($comentario->user_rol);

                return $comentario;
            });

        $foros = DB::table('foro')
            ->select('foro.*', 'users.name as nombre_usuario')
            ->leftJoin('users', 'foro.id_usuario', '=', 'users.id')
            ->orderByDesc('fecha')
            ->get();

        $categorias = $foros->groupBy('id_blog');

        if (!isset($categorias[$id_blog])) {
            abort(404);
        }

        $categoria = (object)[
            'titulo' => $categorias[$id_blog]->first()->titulo,
            'descripcion' => $categorias[$id_blog]->first()->subtitulo,
            'foto' => $categorias[$id_blog]->first()->foto,
            'foros' => $categorias[$id_blog]
        ];

        return view('showcomentario', compact('post', 'categoria', 'ciudades', 'comentarios', 'foro', 'sectores', 'nacionalidades'));
    }
    public function update(Request $request, $id)
    {
        \Log::info('Actualizando post:', $request->all());

        // Validar el post
        $validatedData = $request->validate([
            'comentario' => 'required',
            'is_fixed' => 'nullable|boolean'  // Validación para el checkbox
        ]);

        try {
            // Actualizar el post
            DB::table('posts')
                ->where('id', $id)
                ->update([
                    'comentario' => $request->comentario,
                    'is_fixed' => $request->has('is_fixed'), // Procesar el checkbox
                    'updated_at' => now()
                ]);

            \Log::info('Post actualizado: ' . $id);

            return redirect()->back()
                ->with('success', 'Post actualizado exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar post: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el post');
        }
    }

    public function toggleFixed(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Verificar si el post existe
            $post = Posts::findOrFail($id);

            // Loggear el estado actual
            \Log::info("Toggling fixed status for post {$id}. Current status: " . ($post->is_fixed ? 'true' : 'false'));

            // Cambiar el estado
            $post->is_fixed = !$post->is_fixed;
            $post->save();

            // Loggear el nuevo estado
            \Log::info("New fixed status for post {$id}: " . ($post->is_fixed ? 'true' : 'false'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $post->is_fixed ? 'Post fijado correctamente' : 'Post desfijado correctamente',
                'is_fixed' => $post->is_fixed
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error toggling fixed status for post {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado del post: ' . $e->getMessage()
            ], 500);
        }
    }
}
