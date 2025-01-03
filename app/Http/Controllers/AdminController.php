<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SeoTemplate;
use Illuminate\Support\Facades\Log;
use App\Models\UsuarioPublicate;
use App\Models\Ciudad; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // Obtener usuarios con estadop = 0
        $usuariosInactivos = UsuarioPublicate::where('estadop', 0)
            ->select('id', 'fantasia', 'nombre','edad', 'ubicacion', 'categorias', 'estadop', 'posicion', 'precio')
            ->get();
        
        // Obtener usuarios con estadop = 1
        $usuariosActivos = UsuarioPublicate::whereIn('estadop', [1, 3])
        ->select('id', 'fantasia', 'nombre', 'edad', 'ubicacion', 'categorias', 'estadop', 'posicion', 'precio')
        ->orderBy('posicion', 'asc')  // Esto ordenará por posición de mayor a menor
        ->get();
        
        // Obtener el usuario autenticado
        $usuarioAutenticado = Auth::user();
    
        // Pasar los datos a la vista
        return view('admin.dashboard', compact('usuariosInactivos', 'usuariosActivos', 'usuarioAutenticado'));
    }
    
    public function Perfiles()
    {
        $perfilesRol2 = User::select('users.*', 'usuarios_publicate.estadop as publicate_estado')
            ->leftJoin('usuarios_publicate', function($join) {
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
        $allTemplates = SeoTemplate::select('id', 'tipo', 'filtro', 'ciudad_id', 'description_template')
            ->orderBy('ciudad_id')
            ->orderBy('tipo')
            ->get();
            
        foreach ($allTemplates as $template) {
            if ($template->filtro) {
                $templates['filtros'][$template->filtro][$template->ciudad_id] = $template->description_template;
            } else {
                $templates[$template->tipo][$template->ciudad_id] = $template->description_template;
            }
        }
        
        $defaultTemplates = [
            'ciudad' => 'Explora escorts en {ciudad}.',
            'nacionalidad' => 'Encuentra escorts {nacionalidad} en {ciudad}.',
            'edad' => 'Descubre escorts en {ciudad} de {edad_min} a {edad_max} años.',
            'precio' => 'Encuentra escorts con precios desde {precio_min} hasta {precio_max} en {ciudad}.',
            'atributos' => 'Escorts en {ciudad} con atributos como {atributos}.',
            'servicios' => 'Escorts en {ciudad} que ofrecen servicios como {servicios}.',
            'disponible' => 'Escorts en {ciudad} con disponibilidad: {disponible}.',
            'resena' => 'Encuentra escorts en {ciudad} con estado de reseñas: {resena}.',
            'categorias' => 'Explora escorts en {ciudad} clasificadas en {categorias}.',
            'single' => 'Descubre escorts únicas en {ciudad}.',
            'multiple' => 'Encuentra escorts con tus filtros favoritos en {ciudad}.',
            'complex' => 'Personaliza tu búsqueda y encuentra escorts en {ciudad} que se adapten a todas tus preferencias.'
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
            'tipo' => 'nullable|in:single,multiple,complex',
            'filtro' => 'nullable|string',
            'ciudad_id' => 'required|exists:ciudades,id'
        ]);

        try {
            SeoTemplate::updateOrCreate(
                [
                    'ciudad_id' => $request->ciudad_id,
                    'tipo' => $request->tipo,
                    'filtro' => $request->filtro
                ],
                [
                    'description_template' => $request->description_template
                ]
            );

            return redirect()->back()->with('success', 'Template SEO actualizado correctamente');

        } catch (\Exception $e) {
            Log::error('Error en template SEO', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()
                ->with('error', 'Error al procesar el template SEO: ' . $e->getMessage());
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
    $request->validate([
        'ciudad_id' => 'required|exists:ciudades,id',
        'templates' => 'required|array',
        'templates.*.tipo' => 'required|string',
        'templates.*.description_template' => 'required|string',
    ]);

    try {
        DB::beginTransaction();

        foreach ($request->templates as $templateData) {
            // Lista de tipos que son filtros
            $filtroTypes = [
                'ciudad', 'nacionalidad', 'edad', 'precio', 
                'atributos', 'servicios', 'disponible', 
                'resena', 'categorias'
            ];

            // Determinar si es un filtro o un tipo general
            $isFilter = in_array($templateData['tipo'], $filtroTypes);

            if ($isFilter) {
                // Si es un filtro, guardamos el tipo como 'filtro'
                SeoTemplate::updateOrCreate(
                    [
                        'ciudad_id' => $request->ciudad_id,
                        'tipo' => 'filtro',
                        'filtro' => $templateData['tipo']
                    ],
                    [
                        'description_template' => $templateData['description_template']
                    ]
                );
            } else {
                // Si es un tipo general, guardamos sin filtro
                SeoTemplate::updateOrCreate(
                    [
                        'ciudad_id' => $request->ciudad_id,
                        'tipo' => $templateData['tipo'],
                        'filtro' => null
                    ],
                    [
                        'description_template' => $templateData['description_template']
                    ]
                );
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Templates actualizados correctamente'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al actualizar templates SEO', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar los templates: ' . $e->getMessage()
        ], 500);
    }
}

}