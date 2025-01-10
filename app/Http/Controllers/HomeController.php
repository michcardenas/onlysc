<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\MetaTag; // Agregamos el modelo MetaTag

class HomeController extends Controller 
{
    public function showHome()
    {
        // Obtener las ciudades
        $ciudades = Ciudad::all();
        
        // Obtener los meta datos específicos para 'home'
        $meta = MetaTag::where('page', 'home')->first();

        // Si no existe un registro de meta para home, creamos uno vacío
        if (!$meta) {
            $meta = new MetaTag([
                'page' => 'home',
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'meta_robots' => 'index, follow',
                'heading_h1' => '',
                'heading_h2' => '',
                'additional_text' => ''
            ]);
        }

        // Pasamos tanto las ciudades como los meta datos a la vista
        return view('home', compact('ciudades', 'meta'));
    }
}