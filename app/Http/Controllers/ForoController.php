<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad; // O el modelo que estés usando

class ForoController extends Controller
{
    public function showForo()
    {
        // Lógica de consulta a la base de datos
        $ciudades = Ciudad::all();  // Ejemplo: obtienes todas las ciudades

        // Retorna la vista con los datos consultados
        return view('foro', ['ciudades' => $ciudades]);
    }
    
    public function show_foro($categoria)
    {
        // Agregar la consulta de ciudades aquí también
        $ciudades = Ciudad::all();

        $categorias = [
            'conversaciones' => [
                'titulo' => 'Conversaciones sobre Sexo',
                'descripcion' => 'Bienvenidos a "Charla sobre Sexo", un espacio sin tabúes para discutir todo lo relacionado con la sexualidad.',
                'imagen' => 'foro1.jpg'
            ],
            'experiencias' => [
                'titulo' => 'Experiencias',
                'descripcion' => 'Descubre y comparte tu experiencia con las chicas de la plataforma.',
                'imagen' => 'foro2.jpeg'
            ],
            'gentlemens-club' => [
                'titulo' => "Gentlemen's Club",
                'descripcion' => 'Para hablar con libertad de lo que desees.',
                'imagen' => 'pexels-79380313-9007274-scaled.jpg'
            ]
        ];
    
        if (!isset($categorias[$categoria])) {
            abort(404);
        }
    
        return view('layouts.show_foro', [
            'categoria' => (object)$categorias[$categoria],
            'ciudades' => $ciudades
        ]);
    }
}