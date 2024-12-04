<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioPublicate; 
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // Obtener usuarios con estadop = 0
        $usuariosInactivos = UsuarioPublicate::where('estadop', 0)
            ->select('id', 'fantasia', 'nombre','edad', 'ubicacion', 'categorias', 'estadop', 'posicion', 'precio')
            ->get();
        
        // Obtener usuarios con estadop = 1
        $usuariosActivos = UsuarioPublicate::whereIn('estadop', [1, 3])
        ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'categorias', 'estadop', 'posicion', 'precio')
        ->orderBy('posicion', 'asc')  // Esto ordenará por posición de mayor a menor
        ->get();
        
        // Obtener el usuario autenticado
        $usuarioAutenticado = Auth::user();
    
        // Pasar los datos a la vista
        return view('admin.dashboard', compact('usuariosInactivos', 'usuariosActivos', 'usuarioAutenticado'));
    }
    
    
}
