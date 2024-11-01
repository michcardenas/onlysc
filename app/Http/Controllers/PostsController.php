<?php

namespace App\Http\Controllers;

use App\Models\Posts; // Cambiado de Comentario a Post
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ciudad;

class PostsController extends Controller // Cambiado de ComentarioController a PostController
{
    public function store(Request $request)
    {
        // Validar el post
        $request->validate([
            'comentario' => 'required',
            'id_blog' => 'required|exists:foro,id_blog'
        ]);

        // Obtener el foro al que pertenece el post
        $foro = DB::table('foro')
            ->where('id_blog', $request->id_blog)
            ->first();

        if (!$foro) {
            return redirect()->back()->with('error', 'Foro no encontrado');
        }

        // Insertar el post
        DB::table('posts')->insert([ // Cambiado 'comentarios' a 'posts'
            'id_blog' => $request->id_blog,
            'id_usuario' => auth()->id(),
            'comentario' => $request->comentario,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Redirigir a la misma página del foro
        return redirect()->to(url()->previous() . '#posts') // Cambiado '#comentarios' a '#posts'
            ->with('success', 'Post publicado exitosamente');
    }

    public function showPost($id_blog, $id)
{
    // Obtener todas las ciudades
    $ciudades = Ciudad::all();

    // Obtener el post específico
    $post = DB::table('posts as p')
        ->select('p.*', 'u.name as nombre_usuario', 'p.id_blog')
        ->leftJoin('users as u', 'p.id_usuario', '=', 'u.id')
        ->where('p.id', $id)
        ->first();

    if (!$post) {
        return redirect()->route('foro')->with('error', 'Post no encontrado');
    }

    // Obtener el foro específico
    $foro = DB::table('foro')
        ->where('id', $id_blog)
        ->first();

    // Obtener los comentarios del post
    $comentarios = DB::table('comentario as c')
        ->select('c.*', 'u.name as nombre_usuario')
        ->leftJoin('users as u', 'c.id_usuario', '=', 'u.id')
        ->where('c.id_post', $id)
        ->orderBy('c.created_at', 'desc')
        ->get();

    // Obtener todos los foros y agruparlos por id_blog
    $foros = DB::table('foro')
        ->select('foro.*', 'users.name as nombre_usuario')
        ->leftJoin('users', 'foro.id_usuario', '=', 'users.id')
        ->orderBy('fecha', 'desc')
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

    // Pasar todas las variables a la vista, incluyendo foro
    return view('layouts.showcomentario', compact('post', 'categoria', 'ciudades', 'comentarios', 'foro'));
}
}
