<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MetaTag;
use App\Models\Ciudad;
use Illuminate\Support\Facades\Storage;


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
            
        
              $meta = MetaTag::where('page', 'blog')->first();
              if (!$meta) {
                  $meta = new MetaTag();
                  $meta->page = 'blogadmin';
              }
              
              return view('seo.blogadmin', compact('meta'));
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
    public function showRobots()
    {
        $content = Storage::disk('local')->exists('robots.txt') 
            ? Storage::disk('local')->get('robots.txt') 
            : "User-agent: *\nDisallow:";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }

    // Mostrar formulario de edición para el robots.txt
    public function editRobots()
    {
        $content = Storage::disk('local')->exists('robots.txt') 
            ? Storage::disk('local')->get('robots.txt') 
            : "User-agent: *\nDisallow:";

        return view('seo.edit_robots', compact('content'));
    }

    // Guardar cambios en robots.txt
    public function updateRobots(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        Storage::disk('local')->put('robots.txt', $validated['content']);

        return redirect()->route('seo.edit_robots')->with('success', 'El archivo robots.txt se ha actualizado correctamente.');
    }
}
