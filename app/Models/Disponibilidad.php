<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilidad extends Model
{
    use HasFactory;

    protected $table = 'disponibilidad';

    protected $fillable = [
        'publicate_id',
        'dia',
        'hora_desde',
        'hora_hasta',
        'estado'
    ];

    /**
     * Obtener el usuario publicado al que pertenece esta disponibilidad
     */
    public function publicate()
    {
        return $this->belongsTo(UsuarioPublicate::class, 'publicate_id');
    }

    /**
     * Los días permitidos para la disponibilidad
     */
    public static $diasPermitidos = [
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes',
        'Sábado',
        'Domingo'
    ];
}