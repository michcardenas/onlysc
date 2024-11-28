<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Posts;
use App\Models\Foro;
use App\Models\User;

class Comentario extends Model
{
    protected $table = 'comentario';
    
    protected $fillable = [
        'id_blog',
        'id_post',
        'id_usuario',
        'comentario'
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

    public function post()
    {
        return $this->belongsTo(Posts::class, 'id_post');
    }
}