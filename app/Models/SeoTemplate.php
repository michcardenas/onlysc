<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoTemplate extends Model
{
    protected $table = 'seo_templates';
    
    protected $fillable = [
        'tipo',
        'description_template'
    ];
}
