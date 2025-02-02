<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sectores';

    protected $fillable = [
        'nombre',
        'url'
    ];

    // En app/Models/Sector.php
    public function metaTag()
    {
        return $this->hasOne(MetaTag::class, 'page', 'id')
            ->where('tipo', 'sectores');
    }

    public function usuariosPublicate() {
        return $this->hasMany(UsuarioPublicate::class, 'sectores');
    }
}
