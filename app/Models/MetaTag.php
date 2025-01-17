<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'page', 
        'meta_title', 
        'meta_description', 
        'meta_keywords', 
        'meta_author', 
        'meta_robots', 
        'canonical_url',
        'heading_h1',
        'heading_h2',
        'heading_h2_secondary', 
        'additional_text',
        'additional_text_more',
        'fondo',
        'texto_zonas', // Nueva columna
        'titulo_tarjetas'
    ];
}
