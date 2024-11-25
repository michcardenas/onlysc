<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = ['nombre', 'slug', 'descripcion'];

    // Relación muchos a muchos con artículos
    public function articles()
    {
        return $this->belongsToMany(BlogArticle::class, 'blog_article_category', 'blog_category_id', 'blog_article_id');
    }

    // Genera automáticamente el slug al establecer el nombre
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->nombre);
            }
        });
    }
}