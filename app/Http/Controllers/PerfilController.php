<?php
namespace App\Http\Controllers;

use App\Models\UsuarioPublicate;
use App\Models\Ciudad;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function show($id)
    {
        // Obtener todas las ciudades
        $ciudades = Ciudad::all();

        // Buscar el usuario en la base de datos por ID
        $usuarioPublicate = UsuarioPublicate::findOrFail($id);

        // Retornar la vista y pasar la información del usuario y las ciudades
        return view('layouts.showescort', compact('usuarioPublicate', 'ciudades'));
    }
}
