<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    protected $fillable = [
        'nombre',
        'posicion',
        'url'
    ];

    public function metaTag()
    {
        return $this->hasOne(MetaTag::class, 'tipo_id')->where('tipo', 'atributos');
    }
}
