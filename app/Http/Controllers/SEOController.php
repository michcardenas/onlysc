<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\MetaTag;
use App\Models\Ciudad;
use App\Models\Servicio;
use App\Models\Sector;
use App\Models\Atributo;
use App\Models\Nacionalidad;
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


    public function templatesUnitarios()
    {
        $usuarioAutenticado = Auth::user();
        $servicios = Servicio::all();
        $ciudades = Ciudad::all();
        $atributos = Atributo::all();
        $nacionalidades = Nacionalidad::all();
        $sectores = Sector::all(); // Agregar esta línea

        return view('seo.templateunitarios', compact(
            'usuarioAutenticado',
            'servicios',
            'atributos',
            'ciudades',
            'nacionalidades',
            'sectores' // Agregar esta línea
        ));
    }

    public function getServicioSeo(Servicio $servicio, Request $request)
    {
        $ciudadId = $request->query('ciudad_id');
        $page = $ciudadId ? "seo/servicios/{$servicio->id}/ciudad/{$ciudadId}" : "seo/servicios/{$servicio->id}";
    
        $seo = MetaTag::where('page', $page)
            ->where('tipo', 'servicios')
            ->first();
    
        if (!$seo) {
            return response()->json([
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'canonical_url' => '',
                'meta_robots' => 'index, follow',
                'heading_h1' => '',
                'heading_h2' => '',
                'additional_text' => ''
            ]);
        }
    
        return response()->json($seo);
    }
    
    public function getSectorSeo(Sector $sector, Request $request)
    {
        $ciudadId = $request->query('ciudad_id');
        $page = $ciudadId ? "seo/sectores/{$sector->id}/ciudad/{$ciudadId}" : "seo/sectores/{$sector->id}";
    
        $seo = MetaTag::where('page', $page)
            ->where('tipo', 'sectores')
            ->first();
    
        if (!$seo) {
            return response()->json([
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'canonical_url' => '',
                'meta_robots' => 'index, follow',
                'heading_h1' => '',
                'heading_h2' => '',
                'additional_text' => ''
            ]);
        }
    
        return response()->json($seo);
    }
    
    public function getAtributoSeo(Atributo $atributo, Request $request)
    {
        $ciudadId = $request->query('ciudad_id');
        $page = $ciudadId ? "seo/atributos/{$atributo->id}/ciudad/{$ciudadId}" : "seo/atributos/{$atributo->id}";
    
        $seo = MetaTag::where('page', $page)
            ->where('tipo', 'atributos')
            ->first();
    
        if (!$seo) {
            return response()->json([
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'canonical_url' => '',
                'meta_robots' => 'index, follow',
                'heading_h1' => '',
                'heading_h2' => '',
                'additional_text' => ''
            ]);
        }
    
        return response()->json($seo);
    }
    
    public function getNacionalidadSeo(Nacionalidad $nacionalidad, Request $request)
    {
        $ciudadId = $request->query('ciudad_id');
        $page = $ciudadId ? "seo/nacionalidades/{$nacionalidad->id}/ciudad/{$ciudadId}" : "seo/nacionalidades/{$nacionalidad->id}";
    
        $seo = MetaTag::where('page', $page)
            ->where('tipo', 'nacionalidades')
            ->first();
    
        if (!$seo) {
            return response()->json([
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'canonical_url' => '',
                'meta_robots' => 'index, follow',
                'heading_h1' => '',
                'heading_h2' => '',
                'additional_text' => ''
            ]);
        }
    
        return response()->json($seo);
    }
    
    public function updateServicioSeo(Request $request)
    {
        try {
            Log::info('Iniciando actualización de SEO para servicio', [
                'servicio_id' => $request->servicio_id,
                'ciudad_id' => $request->ciudad_id,
                'ip' => $request->ip()
            ]);
    
            $servicio = Servicio::findOrFail($request->servicio_id);
            Log::info('Servicio encontrado', ['servicio' => $servicio->nombre]);
    
            $validated = $request->validate([
                'ciudad_id' => 'required|exists:ciudades,id',
                'meta_title' => 'required|max:60',
                'meta_description' => 'required|max:160',
                'meta_keywords' => 'nullable',
                'canonical_url' => 'nullable|url',
                'meta_robots' => 'required',
                'heading_h1' => 'nullable|max:255',
                'heading_h2' => 'nullable|max:255',
                'additional_text' => 'nullable'
            ]);
    
            Log::info('Datos validados correctamente', [
                'meta_title' => $validated['meta_title'],
                'meta_description' => substr($validated['meta_description'], 0, 50) . '...'
            ]);
    
            $page = "seo/servicios/{$servicio->id}/ciudad/{$request->ciudad_id}";
    
            $seo = MetaTag::updateOrCreate(
                [
                    'page' => $page,
                    'tipo' => 'servicios'
                ],
                $validated
            );
    
            Log::info('SEO actualizado exitosamente', [
                'seo_id' => $seo->id,
                'page' => $seo->page
            ]);
    
            return response()->json(['success' => true, 'data' => $seo]);
        } catch (\Exception $e) {
            Log::error('Error actualizando SEO', [
                'servicio_id' => $request->servicio_id,
                'ciudad_id' => $request->ciudad_id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateAtributoSeo(Request $request)
    {
        try {
            Log::info('Iniciando actualización de SEO para atributo', [
                'atributo_id' => $request->atributo_id,
                'ciudad_id' => $request->ciudad_id,
                'ip' => $request->ip()
            ]);
    
            $atributo = Atributo::findOrFail($request->atributo_id);
            Log::info('Atributo encontrado', ['atributo' => $atributo->nombre]);
    
            $validated = $request->validate([
                'ciudad_id' => 'required|exists:ciudades,id',
                'meta_title' => 'required|max:60',
                'meta_description' => 'required|max:160',
                'meta_keywords' => 'nullable',
                'canonical_url' => 'nullable|url',
                'meta_robots' => 'required',
                'heading_h1' => 'nullable|max:255',
                'heading_h2' => 'nullable|max:255',
                'additional_text' => 'nullable'
            ]);
    
            $page = "seo/atributos/{$atributo->id}/ciudad/{$request->ciudad_id}";
    
            $seo = MetaTag::updateOrCreate(
                [
                    'page' => $page,
                    'tipo' => 'atributos'
                ],
                $validated
            );
    
            Log::info('SEO actualizado exitosamente', [
                'seo_id' => $seo->id,
                'page' => $seo->page
            ]);
    
            return response()->json(['success' => true, 'data' => $seo]);
        } catch (\Exception $e) {
            Log::error('Error actualizando SEO', [
                'atributo_id' => $request->atributo_id,
                'ciudad_id' => $request->ciudad_id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateNacionalidadSeo(Request $request)
    {
        try {
            Log::info('Iniciando actualización de SEO para nacionalidad', [
                'nacionalidad_id' => $request->nacionalidad_id,
                'ciudad_id' => $request->ciudad_id,
                'ip' => $request->ip()
            ]);
    
            $nacionalidad = Nacionalidad::findOrFail($request->nacionalidad_id);
            Log::info('Nacionalidad encontrada', ['nacionalidad' => $nacionalidad->nombre]);
    
            $validated = $request->validate([
                'ciudad_id' => 'required|exists:ciudades,id',
                'meta_title' => 'required|max:60',
                'meta_description' => 'required|max:160',
                'meta_keywords' => 'nullable',
                'canonical_url' => 'nullable|url',
                'meta_robots' => 'required',
                'heading_h1' => 'nullable|max:255',
                'heading_h2' => 'nullable|max:255',
                'additional_text' => 'nullable'
            ]);
    
            $page = "seo/nacionalidades/{$nacionalidad->id}/ciudad/{$request->ciudad_id}";
    
            $seo = MetaTag::updateOrCreate(
                [
                    'page' => $page,
                    'tipo' => 'nacionalidades'
                ],
                $validated
            );
    
            Log::info('SEO actualizado exitosamente', [
                'seo_id' => $seo->id,
                'page' => $seo->page
            ]);
    
            return response()->json(['success' => true, 'data' => $seo]);
        } catch (\Exception $e) {
            Log::error('Error actualizando SEO', [
                'nacionalidad_id' => $request->nacionalidad_id,
                'ciudad_id' => $request->ciudad_id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateSectorSeo(Request $request)
    {
        try {
            Log::info('Iniciando actualización de SEO para sector', [
                'sector_id' => $request->sector_id,
                'ciudad_id' => $request->ciudad_id,
                'ip' => $request->ip()
            ]);
    
            $sector = Sector::findOrFail($request->sector_id);
            Log::info('Sector encontrado', ['sector' => $sector->nombre]);
    
            $validated = $request->validate([
                'ciudad_id' => 'required|exists:ciudades,id',
                'meta_title' => 'required|max:60',
                'meta_description' => 'required|max:160',
                'meta_keywords' => 'nullable',
                'canonical_url' => 'nullable|url',
                'meta_robots' => 'required',
                'heading_h1' => 'nullable|max:255',
                'heading_h2' => 'nullable|max:255',
                'additional_text' => 'nullable'
            ]);
    
            $page = "seo/sectores/{$sector->id}/ciudad/{$request->ciudad_id}";
    
            $seo = MetaTag::updateOrCreate(
                [
                    'page' => $page,
                    'tipo' => 'sectores'
                ],
                $validated
            );
    
            Log::info('SEO actualizado exitosamente', [
                'seo_id' => $seo->id,
                'page' => $seo->page
            ]);
    
            return response()->json(['success' => true, 'data' => $seo]);
        } catch (\Exception $e) {
            Log::error('Error actualizando SEO', [
                'sector_id' => $request->sector_id,
                'ciudad_id' => $request->ciudad_id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // Funciones para Disponibilidad
public function getDisponibilidadSeo(Request $request)
{
    $ciudadId = $request->query('ciudad_id');
    $page = $ciudadId ? "seo/disponibilidad/ciudad/{$ciudadId}" : "seo/disponibilidad";

    $seo = MetaTag::where('page', $page)
        ->where('tipo', 'disponibilidad')
        ->first();

    if (!$seo) {
        return response()->json([
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'canonical_url' => '',
            'meta_robots' => 'index, follow',
            'heading_h1' => '',
            'heading_h2' => '',
            'additional_text' => ''
        ]);
    }

    return response()->json($seo);
}

public function updateDisponibilidadSeo(Request $request)
{
    try {
        Log::info('Iniciando actualización de SEO para disponibilidad', [
            'ciudad_id' => $request->ciudad_id,
            'ip' => $request->ip()
        ]);

        $validated = $request->validate([
            'ciudad_id' => 'required|exists:ciudades,id',
            'meta_title' => 'required|max:60',
            'meta_description' => 'required|max:160',
            'meta_keywords' => 'nullable',
            'canonical_url' => 'nullable|url',
            'meta_robots' => 'required',
            'heading_h1' => 'nullable|max:255',
            'heading_h2' => 'nullable|max:255',
            'additional_text' => 'nullable'
        ]);

        $page = "seo/disponibilidad/ciudad/{$request->ciudad_id}";

        $seo = MetaTag::updateOrCreate(
            [
                'page' => $page,
                'tipo' => 'disponibilidad'
            ],
            $validated
        );

        Log::info('SEO de disponibilidad actualizado exitosamente', [
            'seo_id' => $seo->id,
            'ciudad_id' => $request->ciudad_id
        ]);

        return response()->json(['success' => true, 'data' => $seo]);
    } catch (\Exception $e) {
        Log::error('Error actualizando SEO de disponibilidad', [
            'ciudad_id' => $request->ciudad_id,
            'error' => $e->getMessage(),
            'linea' => $e->getLine(),
            'archivo' => $e->getFile()
        ]);
        return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
    }
}

// Funciones para Reseñas
public function getResenasSeo(Request $request)
{
    $ciudadId = $request->query('ciudad_id');
    $page = $ciudadId ? "seo/resenas/ciudad/{$ciudadId}" : "seo/resenas";

    $seo = MetaTag::where('page', $page)
        ->where('tipo', 'resenas')
        ->first();

    if (!$seo) {
        return response()->json([
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'canonical_url' => '',
            'meta_robots' => 'index, follow',
            'heading_h1' => '',
            'heading_h2' => '',
            'additional_text' => ''
        ]);
    }

    return response()->json($seo);
}

public function updateResenasSeo(Request $request)
{
    try {
        Log::info('Iniciando actualización de SEO para reseñas', [
            'ciudad_id' => $request->ciudad_id,
            'ip' => $request->ip()
        ]);

        $validated = $request->validate([
            'ciudad_id' => 'required|exists:ciudades,id',
            'meta_title' => 'required|max:60',
            'meta_description' => 'required|max:160',
            'meta_keywords' => 'nullable',
            'canonical_url' => 'nullable|url',
            'meta_robots' => 'required',
            'heading_h1' => 'nullable|max:255',
            'heading_h2' => 'nullable|max:255',
            'additional_text' => 'nullable'
        ]);

        $page = "seo/resenas/ciudad/{$request->ciudad_id}";

        $seo = MetaTag::updateOrCreate(
            [
                'page' => $page,
                'tipo' => 'resenas'
            ],
            $validated
        );

        Log::info('SEO de reseñas actualizado exitosamente', [
            'seo_id' => $seo->id,
            'ciudad_id' => $request->ciudad_id
        ]);

        return response()->json(['success' => true, 'data' => $seo]);
    } catch (\Exception $e) {
        Log::error('Error actualizando SEO de reseñas', [
            'ciudad_id' => $request->ciudad_id,
            'error' => $e->getMessage(),
            'linea' => $e->getLine(),
            'archivo' => $e->getFile()
        ]);
        return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
    }
}

// Funciones para Categorías
public function getCategoriaSeo($categoria, Request $request)
{
    $ciudadId = $request->query('ciudad_id');
    $page = $ciudadId ? "seo/categorias/{$categoria}/ciudad/{$ciudadId}" : "seo/categorias/{$categoria}";

    $seo = MetaTag::where('page', $page)
        ->where('tipo', 'categorias')
        ->first();

    if (!$seo) {
        return response()->json([
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'canonical_url' => '',
            'meta_robots' => 'index, follow',
            'heading_h1' => '',
            'heading_h2' => '',
            'additional_text' => ''
        ]);
    }

    return response()->json($seo);
}

public function updateCategoriaSeo(Request $request)
{
    try {
        Log::info('Iniciando actualización de SEO para categoría', [
            'categoria' => $request->categoria_id,
            'ciudad_id' => $request->ciudad_id,
            'ip' => $request->ip()
        ]);

        $validated = $request->validate([
            'ciudad_id' => 'required|exists:ciudades,id',
            'categoria_id' => 'required|in:vip,premium,lujo,under,masajes',
            'meta_title' => 'required|max:60',
            'meta_description' => 'required|max:160',
            'meta_keywords' => 'nullable',
            'canonical_url' => 'nullable|url',
            'meta_robots' => 'required',
            'heading_h1' => 'nullable|max:255',
            'heading_h2' => 'nullable|max:255',
            'additional_text' => 'nullable'
        ]);

        $page = "seo/categorias/{$request->categoria_id}/ciudad/{$request->ciudad_id}";

        $seo = MetaTag::updateOrCreate(
            [
                'page' => $page,
                'tipo' => 'categorias'
            ],
            $validated
        );

        Log::info('SEO de categoría actualizado exitosamente', [
            'seo_id' => $seo->id,
            'categoria' => $request->categoria_id,
            'ciudad_id' => $request->ciudad_id
        ]);

        return response()->json(['success' => true, 'data' => $seo]);
    } catch (\Exception $e) {
        Log::error('Error actualizando SEO de categoría', [
            'categoria' => $request->categoria_id,
            'ciudad_id' => $request->ciudad_id,
            'error' => $e->getMessage(),
            'linea' => $e->getLine(),
            'archivo' => $e->getFile()
        ]);
        return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
    }
}
}
