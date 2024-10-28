<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioPublicate; 
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // Obtener todos los usuarios de la tabla usuarios_publicate
        $usuarios = UsuarioPublicate::select('fantasia', 'nombre', 'ubicacion', 'edad')->get();
        
        // Obtener el usuario autenticado
        $usuarioAutenticado = Auth::user();

        // Pasar los datos a la vista
        return view('admin.dashboard', compact('usuarios', 'usuarioAutenticado'));
    }
}
