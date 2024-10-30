<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'comentario' => 'required',
            'id_blog' => 'required|exists:foro,id_blog'
        ]);
    
        DB::table('comentarios')->insert([
            'id_blog' => $request->id_blog,
            'id_usuario' => auth()->id(),
            'comentario' => $request->comentario,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    
        return redirect()->back()->with('success', 'Comentario agregado exitosamente');
    }

