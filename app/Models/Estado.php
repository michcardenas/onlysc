<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = [
        'user_id',
        'usuarios_publicate_id',
        'fotos'
    ];

    /**
     * Obtener el usuario al que pertenece el estado
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el usuario publicate al que pertenece el estado
     */
    public function usuarioPublicate()
    {
        return $this->belongsTo(UsuarioPublicate::class, 'usuarios_publicate_id');
    }

    public function vistoPor()
    {
        return $this->belongsToMany(User::class, 'estado_visto')
                    ->withTimestamps()
                    ->withPivot('visto_at');
    }
}