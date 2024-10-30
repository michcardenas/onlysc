<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad; // O el modelo que estés usando
use App\Models\UsuarioPublicate;


class InicioController extends Controller
{
    
    public function show()
    {
        // Obtener todas las ciudades
        $ciudades = Ciudad::all();
    
        // Obtener usuarios con estadop = 1 y paginación de 9 por página
        $usuarios = UsuarioPublicate::where('estadop', 1)
            ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'fotos', 'categorias')
            ->paginate(12);
    
        // Obtener el usuario destacado con estadop = 3 (asumiendo que solo hay uno)
        $usuarioDestacado = UsuarioPublicate::where('estadop', 3)
            ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'fotos', 'categorias')
            ->first();
    
        return view('inicio', [
            'ciudades' => $ciudades,
            'usuarios' => $usuarios,
            'usuarioDestacado' => $usuarioDestacado,
        ]);
    }
    
    
    
    
    
    
    
}
