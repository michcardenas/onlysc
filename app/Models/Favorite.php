<?php
// app/Models/Favorite.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'usuario_publicate_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usuarioPublicate()
    {
        return $this->belongsTo(UsuarioPublicate::class);
    }
}