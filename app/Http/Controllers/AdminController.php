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
            ->select('id', 'fantasia', 'nombre','edad', 'ubicacion', 'categorias', 'estadop', 'posicion')
            ->get();
        
        // Obtener usuarios con estadop = 1
        $usuariosActivos = UsuarioPublicate::where('estadop', 1)
        ->select('id', 'fantasia', 'nombre','edad', 'ubicacion', 'categorias', 'estadop', 'posicion')
        ->get();
    
        // Obtener el usuario autenticado
        $usuarioAutenticado = Auth::user();
    
        // Pasar los datos a la vista
        return view('admin.dashboard', compact('usuariosInactivos', 'usuariosActivos', 'usuarioAutenticado'));
    }
    
    
}
