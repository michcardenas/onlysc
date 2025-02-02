<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AtributosUrlSeeder extends Seeder
{
    public function run()
    {
        $atributtes = DB::table('atributos')->get();
        
        foreach($atributtes as $atributo) {
            DB::table('atributos')
                ->where('id', $atributo->id)
                ->update([
                    'url' => Str::slug($atributo->nombre)
                ]);
        }
    }
}
