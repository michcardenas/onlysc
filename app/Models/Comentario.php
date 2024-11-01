<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Posts;

class Comentario extends Model
{
    protected $table = 'comentario';

    protected $fillable = [
        'id_blog', // Campo para la relación con Foro
        'id_post', // Campo para la relación con Post
        'id_usuario',
        'comentario'
    ];

    public $timestamps = true; // Activa los timestamps created_at y updated_at

    // Relación con el usuario que hizo el comentario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con la tabla Foro usando id_blog
    public function foro()
    {
        return $this->belongsTo(Foro::class, 'id_blog');
    }

    // Relación con la tabla Post usando id_post
    public function post()
    {
        return $this->belongsTo(Posts::class, 'id_post');
    }
}
