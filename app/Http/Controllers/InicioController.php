<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad; // O el modelo que estés usando

class InicioController extends Controller
{
    public function show()
    {
        // Lógica de consulta a la base de datos
        $ciudades = Ciudad::all();  // Ejemplo: obtienes todas las ciudades

        // Retorna la vista con los datos consultados
        return view('inicio', ['ciudades' => $ciudades]);
    }
}
