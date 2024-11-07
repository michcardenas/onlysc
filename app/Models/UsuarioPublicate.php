<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioPublicate extends Model
{
    use HasFactory;

    protected $table = 'usuarios_publicate';

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
        'estadop',
        'categorias',
        'posicion',
        'precio',
    ];

    /**
     * Obtener la disponibilidad del usuario
     */
    public function disponibilidad()
    {
        return $this->hasMany(Disponibilidad::class, 'publicate_id');
    }
}