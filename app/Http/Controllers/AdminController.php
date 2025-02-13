<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SeoTemplate;
use Illuminate\Support\Facades\Log;
use App\Models\UsuarioPublicate;
use App\Models\Ciudad;
use App\Models\Nacionalidad;
use App\Models\TYC;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Sector;
use App\Models\Servicio;
use App\Models\Atributo;

class AdminController extends Controller
{
    public function index()
    {
        // Obtener todas las ciudades únicas
        $ciudades = UsuarioPublicate::distinct('ubicacion')
            ->pluck('ubicacion')
            ->sort();

        // Obtener usuarios con estadop = 0
        $usuariosInactivos = UsuarioPublicate::where('estadop', 0)
            ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'categorias', 'estadop', 'posicion', 'precio')
            ->get();

        // Obtener usuarios con estadop = 1 o 3
        $usuariosActivos = UsuarioPublicate::whereIn('estadop', [1, 3])
            ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'categorias', 'estadop', 'posicion', 'precio')
            ->orderBy('posicion', 'asc')
            ->get();

        $usuarioAutenticado = Auth::user();

        return view('admin.dashboard', compact('usuariosInactivos', 'usuariosActivos', 'usuarioAutenticado', 'ciudades'));
    }

    public function getUsersByCity(Request $request)
    {
        $ciudad = $request->ciudad;

        $usuariosActivos = UsuarioPublicate::whereIn('estadop', [1, 3])
            ->when($ciudad !== 'todas', function ($query) use ($ciudad) {
                return $query->where('ubicacion', $ciudad);
            })
            ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'categorias', 'estadop', 'posicion', 'precio')
            ->orderBy('posicion', 'asc')
            ->get();

        $usuariosInactivos = UsuarioPublicate::where('estadop', 0)
            ->when($ciudad !== 'todas', function ($query) use ($ciudad) {
                return $query->where('ubicacion', $ciudad);
            })
            ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'categorias', 'estadop', 'posicion', 'precio')
            ->get();

        return response()->json([
            'activos' => view('admin.partials.tabla-usuarios', ['usuarios' => $usuariosActivos])->render(),
            'inactivos' => view('admin.partials.tabla-usuarios', ['usuarios' => $usuariosInactivos])->render()
        ]);
    }

    public function Perfiles()
    {
        $perfilesRol2 = User::select('users.*', 'usuarios_publicate.estadop as publicate_estado')
            ->leftJoin('usuarios_publicate', function ($join) {
                $join->on('users.email', '=', 'usuarios_publicate.email')
                    ->orWhere('users.name', '=', 'usuarios_publicate.nombre')
                    ->orWhere('usuarios_publicate.email', 'LIKE', DB::raw('CONCAT("%", users.email, "%")'));
            })
            ->where('users.rol', 2)
            ->paginate(10);

        $usuarioAutenticado = Auth::user();

        $perfilesRol3 = User::where('rol', 3)->paginate(10);

        return view('admin.perfiles', compact('perfilesRol2', 'perfilesRol3', 'usuarioAutenticado'));
    }

    public function loginAsUser($id)
    {
        try {
            $usuario = User::findOrFail($id);

            // Guardamos el ID del admin original en la sesión
            session(['admin_original_id' => auth()->id()]);

            // Hacemos logout del admin
            auth()->logout();

            // Hacemos login como el usuario seleccionado
            auth()->login($usuario);

            Log::info('Admin logueado como usuario', [
                'admin_id' => session('admin_original_id'),
                'user_id' => $usuario->id
            ]);

            // Redirigir al perfil del usuario
            return redirect()->route('admin.profile')->with('success', 'Ahora estás editando el perfil de ' . $usuario->name);
        } catch (\Exception $e) {
            Log::error('Error al hacer login como usuario', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()->with('error', 'Error al acceder al perfil del usuario');
        }
    }

    public function returnToAdmin()
    {
        try {
            // Verificamos si hay un ID de admin guardado
            if (!session()->has('admin_original_id')) {
                return redirect()->route('admin.perfiles')
                    ->with('error', 'No hay sesión de administrador para restaurar');
            }

            $adminId = session('admin_original_id');
            $admin = User::findOrFail($adminId);

            // Hacemos logout del usuario actual
            auth()->logout();

            // Hacemos login como admin
            auth()->login($admin);

            // Limpiamos la sesión
            session()->forget('admin_original_id');

            Log::info('Admin retornó a su cuenta', [
                'admin_id' => $adminId
            ]);

            return redirect()->route('admin.perfiles')
                ->with('success', 'Has vuelto a tu cuenta de administrador');
        } catch (\Exception $e) {
            Log::error('Error al retornar a cuenta admin', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()->with('error', 'Error al volver a la cuenta de administrador');
        }
    }

    public function eliminarPerfil($id)
    {
        try {
            // Buscar el usuario
            $usuario = User::findOrFail($id);

            // Verificar si el usuario tiene un perfil en usuarios_publicate
            $usuarioPublicate = UsuarioPublicate::where('email', $usuario->email)
                ->orWhere('nombre', $usuario->name)
                ->orWhere('email', 'LIKE', '%' . $usuario->email . '%')
                ->first();

            DB::beginTransaction();

            // Eliminar el perfil de usuarios_publicate si existe
            if ($usuarioPublicate) {
                $usuarioPublicate->delete();
            }

            // Eliminar el usuario
            $usuario->delete();

            DB::commit();

            Log::info('Perfil eliminado exitosamente', [
                'user_id' => $id,
                'admin_id' => auth()->id()
            ]);

            return redirect()->route('admin.perfiles')
                ->with('success', 'Perfil eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar perfil', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar el perfil: ' . $e->getMessage());
        }
    }

    public function seoTemplates()
    {
        $ciudades = Ciudad::all();
        $templates = [];
    
        // Obtener todos los templates y organizarlos por tipo y ciudad
        $allTemplates = SeoTemplate::select('id', 'tipo', 'filtro', 'ciudad_id', 'description_template', 'titulo')
            ->orderBy('ciudad_id')
            ->orderBy('tipo')
            ->get();
    
        foreach ($allTemplates as $template) {
            if ($template->filtro) {
                $templates['filtros'][$template->filtro][$template->ciudad_id] = [
                    'description_template' => $template->description_template,
                    'titulo' => $template->titulo
                ];
            } else {
                $templates[$template->tipo][$template->ciudad_id] = [
                    'description_template' => $template->description_template,
                    'titulo' => $template->titulo
                ];
            }
        }
    
        $defaultTemplates = [
            'ciudad' => [
                'titulo' => 'Escorts en {ciudad}',
                'description_template' => 'Explora escorts en {ciudad}.'
            ],
            'nacionalidad' => [
                'titulo' => 'Escorts {nacionalidad} en {ciudad}',
                'description_template' => 'Encuentra escorts {nacionalidad} en {ciudad}.'
            ],
            'edad' => [
                'titulo' => 'Escorts de {edad_min} a {edad_max} años en {ciudad}',
                'description_template' => 'Descubre escorts en {ciudad} de {edad_min} a {edad_max} años.'
            ],
            'precio' => [
                'titulo' => 'Escorts desde {precio_min} hasta {precio_max} en {ciudad}',
                'description_template' => 'Encuentra escorts con precios desde {precio_min} hasta {precio_max} en {ciudad}.'
            ],
            'atributos' => [
                'titulo' => 'Escorts con {atributos} en {ciudad}',
                'description_template' => 'Escorts en {ciudad} con atributos como {atributos}.'
            ],
            'servicios' => [
                'titulo' => 'Escorts que ofrecen {servicios} en {ciudad}',
                'description_template' => 'Escorts en {ciudad} que ofrecen servicios como {servicios}.'
            ],
            'disponible' => [
                'titulo' => 'Escorts disponibles en {ciudad}',
                'description_template' => 'Escorts en {ciudad} con disponibilidad: {disponible}.'
            ],
            'resena' => [
                'titulo' => 'Escorts con reseñas en {ciudad}',
                'description_template' => 'Encuentra escorts en {ciudad} con estado de reseñas: {resena}.'
            ],
            'categorias' => [
                'titulo' => 'Escorts {categorias} en {ciudad}',
                'description_template' => 'Explora escorts en {ciudad} clasificadas en {categorias}.'
            ],
            'single' => [
                'titulo' => 'Escorts en {ciudad}',
                'description_template' => 'Descubre escorts únicas en {ciudad}.'
            ],
            'multiple' => [
                'titulo' => 'Escorts destacadas en {ciudad}',
                'description_template' => 'Encuentra escorts con tus filtros favoritos en {ciudad}.'
            ],
            'complex' => [
                'titulo' => 'Búsqueda personalizada de escorts en {ciudad}',
                'description_template' => 'Personaliza tu búsqueda y encuentra escorts en {ciudad} que se adapten a todas tus preferencias.'
            ],
            'sector' => [
                'titulo' => 'Escorts en {sector}, {ciudad}',
                'description_template' => '<p>Encuentra escorts en el sector de {sector} en {ciudad}.</p>'
            ]
        ];
    
        return view('seo.templates', [
            'templates' => $templates,
            'defaultTemplates' => $defaultTemplates,
            'usuarioAutenticado' => Auth::user(),
            'ciudades' => $ciudades
        ]);
    }

    public function updateSeoTemplate(Request $request)
    {
        $request->validate([
            'description_template' => 'required|string',
            'titulo' => 'required|string',  // Agregamos validación para título
            'tipo' => 'nullable|in:single,multiple,complex',
            'filtro' => 'nullable|string',
            'ciudad_id' => 'required|exists:ciudades,id'
        ]);
    
        try {
            $template = SeoTemplate::updateOrCreate(
                [
                    'ciudad_id' => $request->ciudad_id,
                    'tipo' => $request->tipo,
                    'filtro' => $request->filtro
                ],
                [
                    'description_template' => $request->description_template,
                    'titulo' => $request->titulo  // Agregamos el título
                ]
            );

            Log::info('SEO template updated successfully', [
                'template_id' => $template->id,
                'ciudad_id' => $request->ciudad_id,
                'tipo' => $request->tipo,
                'titulo' => $request->titulo,
                'filtro' => $request->filtro
            ]);

            return redirect()->back()->with('success', 'Template SEO actualizado correctamente');
        } catch (\Exception $e) {
            Log::error('SEO template update failed', [
                'ciudad_id' => $request->ciudad_id,
                'tipo' => $request->tipo,
                'filtro' => $request->filtro,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error al procesar el template SEO: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un template SEO específico
     */
    public function deleteSeoTemplate($id)
    {
        try {
            $template = SeoTemplate::findOrFail($id);

            // Log antes de eliminar
            Log::info('Template SEO eliminado', [
                'admin_id' => auth()->id(),
                'ciudad_id' => $template->ciudad_id,
                'tipo' => $template->tipo,
                'filtro' => $template->filtro
            ]);

            // Eliminar el template
            $template->delete();

            return redirect()->back()->with('success', 'Template SEO eliminado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar template SEO', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar el template SEO: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los templates de una ciudad específica
     */
    public function getTemplatesByCiudad($ciudadId)
    {
        $templates = SeoTemplate::where('ciudad_id', $ciudadId)
            ->get()
            ->reduce(function ($carry, $template) {
                if ($template->filtro) {
                    $carry['filtros'][$template->filtro] = $template->description_template;
                } else {
                    $carry[$template->tipo] = $template->description_template;
                }
                return $carry;
            }, []);

        return response()->json($templates);
    }

    public function updateAllTemplates(Request $request)
{
    try {
        $request->validate([
            'ciudad_id' => 'required|exists:ciudades,id',
            'templates' => 'required|array'
        ]);

        DB::beginTransaction();

        foreach ($request->templates as $template) {
            if (!isset($template['tipo']) || !isset($template['titulo']) || !isset($template['description_template'])) {
                continue;
            }

            $tipo = $template['tipo'];
            $isFilter = in_array($tipo, [
                'ciudad', 'nacionalidad', 'edad', 'precio', 
                'atributos', 'servicios', 'disponible', 'resena', 
                'sector', 'categorias'
            ]);

            SeoTemplate::updateOrCreate(
                [
                    'ciudad_id' => $request->ciudad_id,
                    'tipo' => $isFilter ? 'filtro' : $tipo,
                    'filtro' => $isFilter ? $tipo : null
                ],
                [
                    'titulo' => $template['titulo'],
                    'description_template' => $template['description_template']
                ]
            );
        }

        DB::commit();
        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error actualizando templates:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar los templates: ' . $e->getMessage()
        ], 500);
    }
}


    public function tycadmin()
    {
        $tyc = TYC::first(); // Asumiendo que tienes un modelo TYC
        $usuarioAutenticado = auth()->user();

        return view('admin.tycadmin', [
            'tyc' => $tyc,
            'usuarioAutenticado' => $usuarioAutenticado
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        // Procesar el contenido para preservar los saltos de línea
        $content = str_replace(["\r\n", "\r"], "\n", $request->content);

        TYC::updateOrCreate(
            ['id' => 1],
            [
                'title' => $request->title,
                'content' => nl2br($content)
            ]
        );

        return redirect()->route('tycadmin')
            ->with('success', 'Términos y condiciones actualizados correctamente');
    }


    // Funciones para Sectores
    public function sectorIndex()
    {
        $sectores = Sector::all();
        return view('sectores.indexsector', compact('sectores'));
    }

    public function sectorCreate()
    {
        return view('sectores.createsector');
    }

    public function sectorStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'url' => 'required'
        ]);

        Sector::create($request->all());

        return redirect()->route('sectores.indexsector')
            ->with('success', 'Sector creado exitosamente.');
    }

    public function sectorEdit(Sector $sector)
    {
        return view('sectores.editsector', compact('sector'));
    }

    public function sectorUpdate(Request $request, Sector $sector)
    {
        $validatedData = $request->validate([
            'nombre' => 'required',
            'url' => 'required'
        ]);
    
        $sector->nombre = $validatedData['nombre'];
        $sector->url = $validatedData['url'];
        $sector->save();
    
        return redirect()->route('sectores.indexsector')
            ->with('success', 'Sector actualizado exitosamente.');
    }

    public function sectorDestroy(Sector $sector)
    {
        $sector->delete();
        return redirect()->route('sectores.indexsector')
            ->with('success', 'Sector eliminado exitosamente.');
    }

    // Funciones para Servicios
    public function servicioIndex()
    {
        $servicios = Servicio::orderBy('posicion')->get();
        return view('servicios.indexservicio', compact('servicios'));
    }

    public function servicioCreate()
    {
        return view('servicios.createservicio');
    }

    // Para Servicios
    public function servicioStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'posicion' => 'required|numeric',
            'url' => 'required'
        ]);

        Servicio::create($request->all());

        return redirect()->route('servicios.indexservicio')
            ->with('success', 'Servicio creado exitosamente.');
    }

    public function servicioUpdate(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre' => 'required',
            'posicion' => 'required|numeric',
            'url' => 'required'
        ]);

        $servicio->update($request->all());

        return redirect()->route('servicios.indexservicio')
            ->with('success', 'Servicio actualizado exitosamente.');
    }

    public function servicioEdit(Servicio $servicio)
    {
        return view('servicios.editservicio', compact('servicio'));
    }


    public function servicioDestroy(Servicio $servicio)
    {
        $servicio->delete();
        return redirect()->route('servicios.indexservicio')
            ->with('success', 'Servicio eliminado exitosamente.');
    }

    // Funciones para Atributos
    public function atributoIndex()
    {
        $atributos = Atributo::orderBy('posicion')->get();
        return view('atributos.indexatributo', compact('atributos'));
    }

    public function atributoCreate()
    {
        return view('atributos.createatributo');
    }

    // Para Atributos
    public function atributoStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'posicion' => 'required|numeric',
            'url' => 'required'
        ]);

        Atributo::create($request->all());

        return redirect()->route('atributos.indexatributo')
            ->with('success', 'Atributo creado exitosamente.');
    }

    public function atributoUpdate(Request $request, Atributo $atributo)
    {
        $request->validate([
            'nombre' => 'required',
            'posicion' => 'required|numeric',
            'url' => 'required'
        ]);

        $atributo->update($request->all());

        return redirect()->route('atributos.indexatributo')
            ->with('success', 'Atributo actualizado exitosamente.');
    }

    public function atributoEdit(Atributo $atributo)
    {
        return view('atributos.editatributo', compact('atributo'));
    }


    public function atributoDestroy(Atributo $atributo)
    {
        $atributo->delete();
        return redirect()->route('atributos.indexatributo')
            ->with('success', 'Atributo eliminado exitosamente.');
    }

    // Funciones para Nacionalidades
    public function nacionalidadIndex()
    {
        $nacionalidades = Nacionalidad::all();
        return view('nacionalidades.indexnacionalidad', compact('nacionalidades'));
    }
    

    public function nacionalidadCreate()
    {
        return view('nacionalidades.createnacionalidad');
    }

    public function nacionalidadStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'url' => 'required'
        ]);

        Nacionalidad::create($request->all());

        return redirect()->route('nacionalidades.indexnacionalidad')
            ->with('success', 'Nacionalidad creada exitosamente.');
    }

    public function nacionalidadEdit(Nacionalidad $nacionalidad)
    {
        return view('nacionalidades.editnacionalidad', compact('nacionalidad'));
    }

    public function nacionalidadUpdate(Request $request, Nacionalidad $nacionalidad)
    {
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'url' => 'required|string|max:255|unique:nacionalidades,url,' . $nacionalidad->id
        ]);
    
        $nacionalidad->update($request->all());
    
        return redirect()->route('nacionalidades.indexnacionalidad')
            ->with('success', 'Nacionalidad actualizada exitosamente.');
    }
    

    public function nacionalidadDestroy(Nacionalidad $nacionalidad)
    {
        $nacionalidad->delete();
        return redirect()->route('nacionalidades.indexnacionalidad')
            ->with('success', 'Nacionalidad eliminada exitosamente.');
    }
}
