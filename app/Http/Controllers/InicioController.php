<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\UsuarioPublicate;

class InicioController extends Controller
{
    public function show()
    {
        // Obtener todas las ciudades
        $ciudades = Ciudad::all();

        // Obtener usuarios con estadop = 1 y paginación de 12 por página
        // Obtener usuarios ordenados por posición
        $usuarios = UsuarioPublicate::where('estadop', 1)
            ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'fotos', 'categorias', 'posicion', 'precio')
            ->orderBy('posicion', 'asc')  // Esto asegura que se ordenen por posición
            ->paginate(12);

        // Obtener el usuario destacado con estadop = 3
        $usuarioDestacado = UsuarioPublicate::where('estadop', 3)
            ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'fotos', 'categorias', 'precio')
            ->first();

        // Obtener usuarios para el panel lateral (11 usuarios)
        $usuariosOnline = UsuarioPublicate::where('estadop', 1)
            ->select('id', 'fantasia', 'edad', 'fotos')
            ->take(11)
            ->get();

        return view('inicio', [
            'ciudades' => $ciudades,
            'usuarios' => $usuarios,
            'usuarioDestacado' => $usuarioDestacado,
            'usuariosOnline' => $usuariosOnline,
            'totalOnline' => $usuariosOnline->count()
        ]);
    }

    public function showPerfil($id)
    {
        $usuario = UsuarioPublicate::findOrFail($id);
        return view('perfil', ['usuario' => $usuario]);
    }
}
