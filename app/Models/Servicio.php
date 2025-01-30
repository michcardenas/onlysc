<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = [
        'nombre',
        'posicion',
        'url'
    ];

    // En el modelo Servicio
    public function metaTag()
    {
        return $this->hasOne(MetaTag::class, 'tipo_id')->where('tipo', 'servicios');
    }
}
