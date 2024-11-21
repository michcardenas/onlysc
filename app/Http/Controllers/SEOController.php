<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SEOController extends Controller
{
    public function index()
    {
        // Obtén el usuario autenticado
        $usuarioAutenticado = Auth::user();

        // Pasa el usuario a la vista
        return view('seo.index', compact('usuarioAutenticado'));
    }
}
