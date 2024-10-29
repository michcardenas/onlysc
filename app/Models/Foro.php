<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foro extends Model
{
    use HasFactory;

    protected $table = 'foro';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'contenido',
        'foto',
        'id_usuario',
        'fecha'
    ];

    protected $dates = [
        'fecha'
    ];

    public $timestamps = false; // Desactivar los timestamps

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}