<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;  // Modelo para la tabla 'ciudades'

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function showHome()
    {
        // Obtener las ciudades desde la base de datos
        $ciudades = Ciudad::all();

        // Retornar la vista 'home.blade.php', pasando la variable $ciudades
        return view('home', compact('ciudades'));
    }
}
