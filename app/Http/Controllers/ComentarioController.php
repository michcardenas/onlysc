<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Foro;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Añade esta línea

class ComentarioController extends Controller
{
    public function showComentario($id_blog, $id_post)
    {
        // Cargar el foro y el post con sus relaciones
        $post = Posts::with(['comentarios.usuario', 'foro'])
                    ->where('id_blog', $id_blog)
                    ->findOrFail($id_post);

        return view('showcomentario', compact('post'));
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

        // Validar la solicitud
        $validated = $request->validate([
            'comentario' => 'required',
            'id_post' => 'required|exists:posts,id',
            'id_blog' => 'required|exists:foro,id'
        ]);

        // Crear el comentario
        $comentario = Comentario::create([
            'id_post' => $request->id_post,
            'id_blog' => $request->id_blog,
            'id_usuario' => Auth::id(),
            'comentario' => $request->comentario
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