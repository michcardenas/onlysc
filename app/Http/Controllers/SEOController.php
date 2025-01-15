<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MetaTag;

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
        $meta = MetaTag::where('page', 'home')->first();
        if (!$meta) {
            $meta = new MetaTag();
            $meta->page = 'home';
        }
    
        return view('seo.home', compact('meta'));
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
