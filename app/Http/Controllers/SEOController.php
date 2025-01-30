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
        $atributos = Atributo::all();
        $nacionalidades = Nacionalidad::all();
        $sectores = Sector::all(); // Agregar esta línea

        return view('seo.templateunitarios', compact(
            'usuarioAutenticado',
            'servicios',
            'atributos',
            'nacionalidades',
            'sectores' // Agregar esta línea
        ));
    }

    public function getSectoresData()
    {
        $sectores = Sector::with(['metaTag'])->get()
            ->map(function ($sector) {
                return [
                    'id' => $sector->id,
                    'nombre' => $sector->nombre,
                    'meta_title' => $sector->metaTag->meta_title ?? '',
                    'meta_description' => $sector->metaTag->meta_description ?? ''
                ];
            });

        return response()->json(['data' => $sectores]);
    }

    public function getNacionalidadesData()
    {
        $nacionalidades = Nacionalidad::with(['metaTag'])->get()
            ->map(function ($nacionalidad) {
                return [
                    'id' => $nacionalidad->id,
                    'nombre' => $nacionalidad->nombre,
                    'meta_title' => $nacionalidad->metaTag->meta_title ?? '',
                    'meta_description' => $nacionalidad->metaTag->meta_description ?? ''
                ];
            });

        return response()->json(['data' => $nacionalidades]);
    }


    public function getServiciosData()
    {
        $servicios = Servicio::with(['metaTag'])->get()
            ->map(function ($servicio) {
                return [
                    'id' => $servicio->id,
                    'nombre' => $servicio->nombre,
                    'meta_title' => $servicio->metaTag->meta_title ?? '',
                    'meta_description' => $servicio->metaTag->meta_description ?? ''
                ];
            });

        return response()->json(['data' => $servicios]);
    }

    public function getAtributosData()
    {
        $atributos = Atributo::with(['metaTag'])->get()
            ->map(function ($atributo) {
                return [
                    'id' => $atributo->id,
                    'nombre' => $atributo->nombre,
                    'meta_title' => $atributo->metaTag->meta_title ?? '',
                    'meta_description' => $atributo->metaTag->meta_description ?? ''
                ];
            });

        return response()->json(['data' => $atributos]);
    }

    public function getServicioSeo(Servicio $servicio)
    {
        $seo = MetaTag::where('page', "seo/servicios/{$servicio->id}")
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

    // Agregar las funciones para obtener y actualizar SEO de sectores
    public function getSectorSeo(Sector $sector)
    {
        $seo = MetaTag::where('page', "seo/sectores/{$sector->id}")
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

    public function getAtributoSeo(Atributo $atributo)
    {
        $seo = MetaTag::where('page', "seo/atributos/{$atributo->id}")
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

    public function updateServicioSeo(Request $request)
    {
        try {
            Log::info('Iniciando actualización de SEO para servicio', [
                'servicio_id' => $request->servicio_id,
                'ip' => $request->ip()
            ]);

            $servicio = Servicio::findOrFail($request->servicio_id);
            Log::info('Servicio encontrado', ['servicio' => $servicio->nombre]);

            $validated = $request->validate([
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

            $seo = MetaTag::updateOrCreate(
                [
                    'page' => "seo/servicios/{$servicio->id}",
                    'tipo' => 'servicios'
                ],
                $validated
            );

            Log::info('SEO actualizado exitosamente', [
                'seo_id' => $seo->id,
                'page' => $seo->page
            ]);

            return response()->json(['success' => true, 'data' => $seo]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en actualización SEO', [
                'servicio_id' => $request->servicio_id,
                'errores' => $e->errors()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Servicio no encontrado', [
                'servicio_id' => $request->servicio_id
            ]);
            return response()->json(['success' => false, 'message' => 'Servicio no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error inesperado actualizando SEO', [
                'servicio_id' => $request->servicio_id,
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);
            return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    public function updateAtributoSeo(Request $request)
    {
        try {
            Log::info('Iniciando actualización de SEO para atributo', [
                'atributo_id' => $request->atributo_id,
                'ip' => $request->ip()
            ]);

            $atributo = Atributo::findOrFail($request->atributo_id);
            Log::info('Atributo encontrado', ['atributo' => $atributo->nombre]);

            $validated = $request->validate([
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

            $seo = MetaTag::updateOrCreate(
                [
                    'page' => "seo/atributos/{$atributo->id}",
                    'tipo' => 'atributos'
                ],
                $validated
            );

            Log::info('SEO actualizado exitosamente', [
                'seo_id' => $seo->id,
                'page' => $seo->page
            ]);

            return response()->json(['success' => true, 'data' => $seo]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en actualización SEO', [
                'atributo_id' => $request->atributo_id,
                'errores' => $e->errors()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Atributo no encontrado', [
                'atributo_id' => $request->atributo_id
            ]);
            return response()->json(['success' => false, 'message' => 'Atributo no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error inesperado actualizando SEO', [
                'atributo_id' => $request->atributo_id,
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);
            return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
    public function getNacionalidadSeo(Nacionalidad $nacionalidad)
    {
        $seo = MetaTag::where('page', "seo/nacionalidades/{$nacionalidad->id}")
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

    public function updateNacionalidadSeo(Request $request)
    {
        try {
            Log::info('Iniciando actualización de SEO para nacionalidad', [
                'nacionalidad_id' => $request->nacionalidad_id,
                'ip' => $request->ip()
            ]);

            $nacionalidad = Nacionalidad::findOrFail($request->nacionalidad_id);
            Log::info('Nacionalidad encontrada', ['nacionalidad' => $nacionalidad->nombre]);

            $validated = $request->validate([
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

            $seo = MetaTag::updateOrCreate(
                [
                    'page' => "seo/nacionalidades/{$nacionalidad->id}",
                    'tipo' => 'nacionalidades'
                ],
                $validated
            );

            Log::info('SEO actualizado exitosamente', [
                'seo_id' => $seo->id,
                'page' => $seo->page
            ]);

            return response()->json(['success' => true, 'data' => $seo]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en actualización SEO', [
                'nacionalidad_id' => $request->nacionalidad_id,
                'errores' => $e->errors()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Nacionalidad no encontrada', [
                'nacionalidad_id' => $request->nacionalidad_id
            ]);
            return response()->json(['success' => false, 'message' => 'Nacionalidad no encontrada'], 404);
        } catch (\Exception $e) {
            Log::error('Error inesperado actualizando SEO', [
                'nacionalidad_id' => $request->nacionalidad_id,
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);
            return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    public function updateSectorSeo(Request $request)
    {
        try {
            $sector = Sector::findOrFail($request->sector_id);

            $validated = $request->validate([
                'meta_title' => 'required|max:60',
                'meta_description' => 'required|max:160',
                'meta_keywords' => 'nullable',
                'canonical_url' => 'nullable|url',
                'meta_robots' => 'required',
                'heading_h1' => 'nullable|max:255',
                'heading_h2' => 'nullable|max:255',
                'additional_text' => 'nullable'
            ]);

            $seo = MetaTag::updateOrCreate(
                [
                    'page' => "seo/sectores/{$sector->id}",
                    'tipo' => 'sectores'
                ],
                $validated
            );

            return response()->json(['success' => true, 'data' => $seo]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
