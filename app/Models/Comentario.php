<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';

    protected $fillable = [
        'id_foro',
        'id_usuario',
        'comentario'
    ];

    // Aquí ya incluimos los timestamps por defecto (created_at y updated_at)
    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function foro()
    {
        return $this->belongsTo(Foro::class, 'id_foro');
    }
}