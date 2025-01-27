<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TYC extends Model
{
    protected $table = 't_y_c';
    
    protected $fillable = [
        'title',
        'content'
    ];
}