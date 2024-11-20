<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Foro;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComentarioController extends Controller
{
    public function showComentario($id_blog, $id_post)
    {
        // Cargar el foro y el post con sus relaciones
        $foro = Foro::findOrFail($id_blog);
        $post = Posts::with(['comentarios.usuario', 'foro'])
                    ->where('id_blog', $id_blog)
                    ->findOrFail($id_post);

        return view('showcomentario', compact('post', 'foro'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Datos recibidos en el controlador:', [
                'request_all' => $request->all(),
                'post_id' => $request->id_post,
                'blog_id' => $request->id_blog,
                'user_id' => Auth::id()
            ]);

            // Limpiar el contenido HTML del comentario
            $comentarioLimpio = strip_tags($request->comentario);
            $request->merge(['comentario' => $comentarioLimpio]);

            // Validar la solicitud con mensajes personalizados
            $validated = $request->validate([
                'comentario' => 'required',
                'id_post' => 'required|exists:posts,id',
                'id_blog' => 'required|exists:foro,id'
            ], [
                'comentario.required' => 'El comentario es requerido.',
                'id_post.required' => 'El ID del post es requerido.',
                'id_blog.required' => 'El ID del blog es requerido.',
                'id_post.exists' => 'El post seleccionado no existe.',
                'id_blog.exists' => 'El blog seleccionado no existe.'
            ]);

            // Verificar que el post pertenece al blog correcto
            $post = Posts::where('id', $request->id_post)
                        ->where('id_blog', $request->id_blog)
                        ->first();

            if (!$post) {
                throw new \Exception('El post no pertenece al blog especificado.');
            }

            // Crear el comentario con el contenido limpio
            $comentario = Comentario::create([
                'id_post' => $request->id_post,
                'id_blog' => $request->id_blog,
                'id_usuario' => Auth::id(),
                'comentario' => $comentarioLimpio
            ]);

            return redirect()->route('post.show', [
                'id_blog' => $request->id_blog,
                'id' => $request->id_post
            ])->with('success', 'Comentario publicado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear comentario: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al crear el comentario: ' . $e->getMessage());
        }
    }
}