<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TYC extends Model
{
    protected $table = 'tyc';
    
    protected $fillable = [
        'title',
        'content'
    ];
}