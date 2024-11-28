<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Añadimos la relación con comentarios
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_usuario');
    }

    // Relación con artículos del blog
    public function blogArticles()
    {
        return $this->hasMany(BlogArticle::class);
    }

    public function isAdmin()
    {
        return $this->rol === '1';
    }
}