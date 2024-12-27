<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SeoTemplate;
use Illuminate\Support\Facades\Log;
use App\Models\UsuarioPublicate; 
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
    $templates = SeoTemplate::all()->pluck('description_template', 'tipo')->toArray();
    $usuarioAutenticado = Auth::user();
    
    return view('seo.templates', compact('templates', 'usuarioAutenticado'));
}

public function updateSeoTemplate(Request $request)
{
    $request->validate([
        'description_template' => 'required|string',
        'tipo' => 'required|in:single,multiple,complex'
    ]);

    try {
        SeoTemplate::updateOrCreate(
            ['tipo' => $request->tipo],
            ['description_template' => $request->description_template]
        );

        Log::info('Template SEO actualizado', [
            'admin_id' => auth()->id(),
            'tipo' => $request->tipo
        ]);

        return redirect()->back()->with('success', 'Contenido SEO actualizado correctamente');

    } catch (\Exception $e) {
        Log::error('Error al actualizar template SEO', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return redirect()->back()
            ->with('error', 'Error al actualizar el contenido SEO: ' . $e->getMessage());
    }
}
}