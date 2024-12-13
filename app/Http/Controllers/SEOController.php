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
    public function home()
    {
        return view('seo.home'); // Vista para el inicio
    }

    public function foroadmin()
    {
        return view('seo.foroadmin'); // Vista para el foro
    }

    public function blogadmin()
    {
        return view('seo.blogadmin'); // Vista para el blog
    }

    public function publicateForm()
    {
        return view('seo.publicate'); // Vista para publicar
    }
}
