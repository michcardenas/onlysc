<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'nacionalidad', // Nueva columna agregada
        'atributos',    // Nueva columna agregada
        'foto_positions',
        'blocked_images',
        'videos',
        'u1',
        'u2',
        'sectores'
    ];

    /**
     * Obtener la disponibilidad del usuario
     */
    public function disponibilidad()
    {
        return $this->hasMany(Disponibilidad::class, 'publicate_id');
    }

    public function estados()
    {
        return $this->hasMany(Estado::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedByUser($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    public function location()
    {
        return $this->hasOne(EscortLocation::class, 'usuario_publicate_id');
    }

    // Método helper para obtener coordenadas
    public function getCoordinates()
    {
        if ($this->location) {
            return [
                'lat' => $this->location->latitud,
                'lng' => $this->location->longitud
            ];
        }
        return null;
    }

    public function posts()
    {
        return $this->hasMany(Posts::class, 'chica_id');
    }

    public function sector() {    // Cambié el nombre a singular ya que es belongsTo
        return $this->belongsTo(Sector::class, 'sectores', 'id');
    }


    // /**
    //  * Get the route key for the model.
    //  * Esto le dice a Laravel que use 'fantasia' en lugar de 'id' para las rutas
    //  */
    // public function getRouteKeyName()
    // {
    //     return 'fantasia';
    // }

    // /**
    //  * Obtener el slug del nombre de fantasía para URLs
    //  */
    // public function getSlugAttribute()
    // {
    //     return Str::slug($this->fantasia);
    // }

    // /**
    //  * Modificar cómo se resuelve el modelo en las rutas
    //  */
    // public function resolveRouteBinding($value, $field = null)
    // {
    //     return $this->where('fantasia', str_replace('-', ' ', $value))->firstOrFail();
    // }
}
