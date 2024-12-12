<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $table = 'posts';
    
    protected $fillable = [
        'titulo',
        'id_blog',
        'id_usuario',
        'comentario',
        'is_fixed',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'is_fixed' => 'boolean'
    ];


    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function foro()
    {
        return $this->belongsTo(Foro::class, 'id_blog');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_post');
    }
}