<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoTemplate extends Model
{
    protected $table = 'seo_templates';

    protected $fillable = [
        'tipo',
        'filtro', // Agregar la nueva columna
        'description_template',
        'ciudad_id',
        'titulo'
    ];

    /**
     * Relación con la tabla Ciudad.
     */
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id', 'id');
    }
}
