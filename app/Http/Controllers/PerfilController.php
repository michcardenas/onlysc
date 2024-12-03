<?php

namespace App\Http\Controllers;

use App\Models\UsuarioPublicate;
use App\Models\Ciudad;
use App\Models\User;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PerfilController extends Controller
{
    public function show($id)
    {
        $ciudades = Ciudad::all();
        $usuarioPublicate = UsuarioPublicate::findOrFail($id);
        return view('layouts.showescort', compact('usuarioPublicate', 'ciudades'));
    }

    public function index()
    {
        $usuarioAutenticado = auth()->user();
        $usuario = User::find($usuarioAutenticado->id);
        return view('admin.perfiladmin', compact('usuario', 'usuarioAutenticado'));
    }

    public function updateProfile(Request $request)
    {
        try {
            $usuario = auth()->user();

            $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
                'password' => 'nullable|string|min:8|confirmed',
                'descripcion' => 'nullable|string',
                'linkedin' => 'nullable|string'
            ]);

            $usuario->name = $request->nombre;
            $usuario->email = $request->email;
            $usuario->linkedin = $request->linkedin;
            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }
            $usuario->descripcion = $request->descripcion;

            Log::info('Intentando actualizar usuario', [
                'user_id' => $usuario->id,
                'data' => $request->all(),
                'linkedin' => $request->linkedin
            ]);

            $saved = $usuario->save();

            Log::info('Resultado de guardado', [
                'success' => $saved,
                'user' => $usuario->toArray()
            ]);

            return redirect()->route('admin.profile')->with('success', 'Perfil actualizado correctamente');
        } catch (\Exception $e) {
            Log::error('Error actualizando perfil', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()->with('error', 'Error al actualizar perfil');
        }
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $usuario = auth()->user();

        if ($request->hasFile('foto')) {
            if ($usuario->foto) {
                Storage::disk('public')->delete($usuario->foto);
            }
            $imagenPath = $request->file('foto')->store('profile_photos', 'public');
            $usuario->foto = $imagenPath;
            $usuario->save();
        }

        return redirect()->route('admin.profile')->with('success', 'Foto de perfil actualizada correctamente');
    }

    public function crearEstado(Request $request)
    {
        try {
            if (auth()->user()->rol != 2) {
                Log::warning('Intento de crear estado por usuario no autorizado', [
                    'user_id' => auth()->id(),
                    'rol' => auth()->user()->rol
                ]);
                return redirect()->back()->with('error', 'No tienes permiso para crear estados');
            }

            $request->validate([
                'fotos.*' => 'required|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:20480'
            ]);

            $user = auth()->user();
            $usuarioPublicate = $this->encontrarUsuarioPublicate($user);

            if (!$usuarioPublicate) {
                return redirect()->back()->with('error', 'No se encontró tu perfil de publicación.');
            }

            if ($request->hasFile('fotos')) {
                $mediaFiles = $this->procesarArchivosMultimedia($request->file('fotos'));

                $estado = Estado::create([
                    'user_id' => $user->id,
                    'usuarios_publicate_id' => $usuarioPublicate->id,
                    'fotos' => json_encode($mediaFiles)
                ]);

                Log::info('Estado creado exitosamente', [
                    'estado_id' => $estado->id,
                    'archivos' => $mediaFiles
                ]);

                return redirect()->back()->with('success', 'Estado creado correctamente');
            }

            return redirect()->back()->with('error', 'No se han proporcionado archivos multimedia');
        } catch (\Exception $e) {
            Log::error('Error al crear estado', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()->with('error', 'Ocurrió un error al crear el estado');
        }
    }

    private function encontrarUsuarioPublicate($user)
    {
        return UsuarioPublicate::where('email', $user->email)
            ->orWhere('nombre', $user->name)
            ->orWhere('email', 'LIKE', '%' . $user->email . '%')
            ->first();
    }

    private function procesarArchivosMultimedia($archivos)
    {
        $mediaFiles = [
            'imagenes' => [],
            'videos' => []
        ];

        foreach ($archivos as $archivo) {
            $extension = strtolower($archivo->getClientOriginalExtension());
            $isVideo = in_array($extension, ['mp4', 'mov', 'avi', 'wmv']);
            $carpeta = $isVideo ? 'estados_videos' : 'estados_fotos';

            // Generar un nombre único para el archivo
            $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;

            // Guardar el archivo con el nombre personalizado
            $filePath = $archivo->storeAs($carpeta, $nombreArchivo, 'public');

            if ($isVideo) {
                $mediaFiles['videos'][] = $filePath;
            } else {
                $mediaFiles['imagenes'][] = $filePath;
            }

            Log::info('Archivo guardado', [
                'path' => $filePath,
                'tipo' => $isVideo ? 'video' : 'imagen',
                'nombre_original' => $archivo->getClientOriginalName()
            ]);
        }

        return $mediaFiles;
    }

    public function eliminarEstado($id)
    {
        try {
            if (auth()->user()->rol != 2) {
                return redirect()->back()->with('error', 'No tienes permiso para eliminar estados');
            }

            $estado = Estado::findOrFail($id);

            if ($estado->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'No tienes permiso para eliminar este estado');
            }

            $this->eliminarArchivosMultimedia($estado);
            $estado->delete();

            return redirect()->back()->with('success', 'Estado eliminado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar estado', [
                'error' => $e->getMessage(),
                'estado_id' => $id
            ]);
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar el estado');
        }
    }

    private function eliminarArchivosMultimedia($estado)
    {
        $mediaFiles = json_decode($estado->fotos, true);
        if (!$mediaFiles) return;

        foreach (['imagenes', 'videos'] as $tipo) {
            foreach ($mediaFiles[$tipo] ?? [] as $archivo) {
                if (Storage::disk('public')->exists($archivo)) {
                    Storage::disk('public')->delete($archivo);
                    Log::info("Archivo eliminado: $archivo");
                }
            }
        }
    }

    public function deleteExpiredEstados()
    {
        try {
            $expiredEstados = Estado::where('created_at', '<=', Carbon::now()->subHours(24))->get();

            foreach ($expiredEstados as $estado) {
                $this->eliminarArchivosMultimedia($estado);
                $estado->delete();

                Log::info('Estado expirado eliminado', [
                    'estado_id' => $estado->id,
                    'created_at' => $estado->created_at
                ]);
            }

            return response()->json(['message' => 'Estados expirados eliminados']);
        } catch (\Exception $e) {
            Log::error('Error eliminando estados expirados', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Error al eliminar estados'], 500);
        }
    }

    public function getUsuario($id) 
{         
    Log::info('Solicitud recibida para usuario ID: ' . $id);
    
    try {
        // Obtenemos el usuario_publicate
        Log::info('Buscando UsuarioPublicate con ID: ' . $id);
        $usuarioPublicate = UsuarioPublicate::findOrFail($id);
        Log::info('UsuarioPublicate encontrado:', ['usuario' => $usuarioPublicate]);

        // Buscamos el usuario relacionado en la tabla users
        Log::info('Buscando User con email: ' . $usuarioPublicate->email);
        $user = User::where('email', $usuarioPublicate->email)
            ->first();
        Log::info('User encontrado:', ['user' => $user]);

        $response = [
            'id' => $usuarioPublicate->id,
            'fantasia' => $usuarioPublicate->fantasia,
            'foto' => $user ? $user->foto : null,
            'created_at' => $usuarioPublicate->created_at
        ];
        
        Log::info('Respuesta preparada:', $response);

        return response()->json($response, 200, ['Content-Type' => 'application/json']);
    } catch (\Exception $e) {
        Log::error('Error al obtener usuario', [
            'error' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
            'usuario_publicate_id' => $id
        ]);

        return response()->json([
            'error' => 'Usuario no encontrado',
            'message' => $e->getMessage()
        ], 404);
    }
}
}
