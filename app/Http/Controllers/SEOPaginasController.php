<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoPagina;

class SEOPaginasController extends Controller
{
    public function index()
    {
        // Supongamos que el usuario autenticado ya está disponible en la sesión
        $usuarioAutenticado = auth()->user(); // Obtiene el usuario autenticado

        return view('seo.paginas', compact('usuarioAutenticado'));
    }
}
