<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ciudad;

class ComentarioController extends Controller
{
    public function store(Request $request)
    {
        // Validar el comentario
        $request->validate([
            'comentario' => 'required',
            'id_blog' => 'required|exists:foro,id_blog'
        ]);

        // Obtener el foro al que pertenece el comentario
        $foro = DB::table('foro')
            ->where('id_blog', $request->id_blog)
            ->first();

        if (!$foro) {
            return redirect()->back()->with('error', 'Foro no encontrado');
        }

        // Insertar el comentario
        DB::table('comentarios')->insert([
            'id_blog' => $request->id_blog,
            'id_usuario' => auth()->id(),
            'comentario' => $request->comentario,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Redirigir a la misma página del foro
        return redirect()->to(url()->previous() . '#comentarios')
            ->with('success', 'Comentario publicado exitosamente');
    }

    
    public function showComentario($id_blog, $id)
{
    // Obtener todas las ciudades
    $ciudades = Ciudad::all();

    // Obtener el comentario específico
    $comentario = DB::table('comentarios as c')
                    ->select('c.*', 'u.name as nombre_usuario', 'c.id_blog')
                    ->leftJoin('users as u', 'c.id_usuario', '=', 'u.id')
                    ->where('c.id', $id)
                    ->first();

    if (!$comentario) {
        return redirect()->route('foro')->with('error', 'Comentario no encontrado');
    }

    // Obtener todos los foros y agruparlos por id_blog
    $foros = DB::table('foro')
        ->select('foro.*', 'users.name as nombre_usuario')
        ->leftJoin('users', 'foro.id_usuario', '=', 'users.id')
        ->orderBy('fecha', 'desc')
        ->get();

    $categorias = $foros->groupBy('id_blog');

    // Verificar si la categoría solicitada existe
    if (!isset($categorias[$id_blog])) {
        abort(404);
    }

    // Obtener la categoría actual
    $categoria = (object)[
        'titulo' => $categorias[$id_blog]->first()->titulo,
        'descripcion' => $categorias[$id_blog]->first()->subtitulo,
        'foto' => $categorias[$id_blog]->first()->foto,
        'foros' => $categorias[$id_blog]
    ];

    return view('layouts.showcomentario', compact('comentario', 'categoria', 'ciudades'));
}


}

