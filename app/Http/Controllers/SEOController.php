<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MetaTag;
use App\Models\Ciudad;

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
        $meta = MetaTag::where('page', 'foro')->first();
        if (!$meta) {
            $meta = new MetaTag();
            $meta->page = 'foro';
        }
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
    public function inicio()
    {
        // Obtener las ciudades (ajusta esto según tu modelo de Ciudad)
        $ciudades = Ciudad::all();
        
        $meta = MetaTag::where('page', 'inicio')->first();
        if (!$meta) {
            $meta = new MetaTag();
            $meta->page = 'inicio-tarjetas';
        }
        
        // Pasar las ciudades a la vista
        return view('seo.inicio-tarjetas', compact('meta', 'ciudades'));
    }

    public function favoritos()
    {
        $meta = MetaTag::where('page', 'favoritos')->first();
        if (!$meta) {
            $meta = new MetaTag();
            $meta->page = 'favoritos';
        }
        return view('seo.favoritos', compact('meta')); // Pasa $meta a la vista
    }
    
}
