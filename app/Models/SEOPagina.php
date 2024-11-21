<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SEOPagina extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'h1',
        'h2',
        'h3',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
}
