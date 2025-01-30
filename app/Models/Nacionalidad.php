<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nacionalidad extends Model
{
    protected $table = 'nacionalidades';

    protected $fillable = [
        'posicion',
        'nombre',
        'url'
    ];

    // En app/Models/Nacionalidad.php
    public function metaTag()
    {
        return $this->hasOne(MetaTag::class, 'page', 'id')
            ->where('tipo', 'nacionalidades');
    }
}
