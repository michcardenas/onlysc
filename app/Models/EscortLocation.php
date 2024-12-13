<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscortLocation extends Model
{
    protected $fillable = [
        'usuario_publicate_id',
        'direccion',
        'ciudad',
        'region',
        'latitud',
        'longitud',
        'referencia',
        'is_approximate'
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'is_approximate' => 'boolean'
    ];

    public function usuarioPublicate()
    {
        return $this->belongsTo(UsuarioPublicate::class);
    }

    // Método para calcular distancia entre dos puntos
    public function distanceFrom($lat, $lng, $unit = 'km')
    {
        $earthRadius = ($unit === 'km') ? 6371 : 3959; // Radio de la Tierra en km o millas

        $latFrom = deg2rad($this->latitud);
        $lonFrom = deg2rad($this->longitud);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            
        return $angle * $earthRadius;
    }
}