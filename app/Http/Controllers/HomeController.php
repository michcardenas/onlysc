<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\MetaTag; // Agregamos el modelo MetaTag
use App\Models\Tarjeta;

class HomeController extends Controller 
{
    public function showHome()
    {
        // Obtener todas las ciudades
        $ciudades = Ciudad::all();
        
        // Agrupar las ciudades por zona
        $ciudadesPorZona = $ciudades->groupBy('zona');
    
        // Obtener todas las tarjetas
        $tarjetas = Tarjeta::all();
        
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
               'heading_h2_secondary' => '', // Agregado nuevo campo
            'additional_text' => '',
            'additional_text_more' => '',
            'fondo' => '',
            'texto_zonas' => '', // Agregado nuevo campo
            'titulo_tarjetas' => '', // Agregado nuevo campo
            'texto_zonas_centro' => '', // Agregado nuevo campo
            'texto_zonas_sur' => '' // Agregado nuevo campo
            ]);
        }
    
        // Pasamos las variables a la vista
        return view('home', compact('ciudades', 'ciudadesPorZona', 'meta', 'tarjetas'));
    }
}