<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug'
    ];

    // Relación con artículos
    public function articles()
    {
        return $this->belongsToMany(BlogArticle::class, 'blog_article_tag', 'tag_id', 'article_id')
                    ->withTimestamps();
    }

    // Obtener solo artículos publicados
    public function publishedArticles()
    {
        return $this->articles()->publicados();
    }

    // Scope para ordenar por nombre
    public function scopeOrdenado($query)
    {
        return $query->orderBy('nombre');
    }

    // Obtener la URL del tag
    public function getUrlAttribute()
    {
        return route('blog.tag', $this->slug);
    }

    // Obtener conteo de artículos publicados
    public function getArticleCountAttribute()
    {
        return $this->publishedArticles()->count();
    }
}