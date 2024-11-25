<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogArticle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'titulo',
        'slug',
        'extracto',
        'contenido',
        'imagen',
        'destacado',
        'estado',
        'fecha_publicacion',
        'visitas'
    ];

    protected $casts = [
        'destacado' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'visitas' => 'integer'
    ];

    // Relación con el usuario (autor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con tags
    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_article_tag', 'article_id', 'tag_id')
            ->withTimestamps();
    }

    // Scope para artículos publicados
    public function scopePublicados($query)
    {
        return $query->where('estado', 'publicado')
            ->whereNotNull('fecha_publicacion')
            ->where('fecha_publicacion', '<=', now());
    }

    // Scope para artículos destacados
    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    // Obtener la URL del artículo
    public function getUrlAttribute()
    {
        return route('blog.show', $this->slug);
    }

    // Obtener la URL de la imagen
    public function getImagenUrlAttribute()
    {
        return $this->imagen
            ? Storage::disk('public')->url($this->imagen)
            : asset('images/default-article.jpg');
    }

    public function categories()
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_article_category', 'blog_article_id', 'blog_category_id');
    }
}
