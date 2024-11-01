<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $table = 'posts'; // Cambiado a la tabla correcta

    protected $fillable = [
        'id_blog', // Asegúrate de que este campo existe en tu tabla posts
        'id_usuario',
        'titulo',
        'contenido'
        // otros campos que necesites
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function foro()
    {
        return $this->belongsTo(Foro::class, 'id_blog');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_post');
    }
}