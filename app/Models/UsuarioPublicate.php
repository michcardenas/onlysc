<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioPublicate extends Model
{
    use HasFactory;

    protected $table = 'usuarios_publicate';

    // Lista de campos que pueden ser llenados en el modelo
    protected $fillable = [
        'fantasia',
        'email',
        'nombre',
        'password',
        'telefono',
        'ubicacion',
        'edad',
        'color_ojos',
        'altura',
        'peso',
        'disponibilidad',
        'servicios',
        'servicios_adicionales',
        'fotos',
        'cuentanos',
        'verificada',
    ];
}
