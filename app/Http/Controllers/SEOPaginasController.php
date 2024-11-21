<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoPagina;

class SEOPaginasController extends Controller
{
    public function index()
    {
        // Obtener todas las páginas SEO
        $seoPaginas = SEOPagina::all();

        // Pasar los datos a la vista
        return view('seo.paginas', compact('seoPaginas'));
    }
}
