<?php

namespace App\Http\Controllers;

use App\Models\UsuarioPublicate;
use App\Models\Ciudad;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use App\Models\MetaTag;
use App\Models\Nacionalidad;
use App\Models\Sector;
use App\Models\Tarjeta;

class PerfilController extends Controller
{
    public function show($id)
    {
        $usuarioPublicate = UsuarioPublicate::with(['location'])->findOrFail($id);
        $ciudades = Ciudad::all();

        // Obtener la ubicación del mapa o valores por defecto para Santiago
        $ubicacion = [
            'direccion' => $usuarioPublicate->location->direccion ?? $usuarioPublicate->ubicacion,
            'lat' => $usuarioPublicate->location->latitud ?? -33.4489,
            'lng' => $usuarioPublicate->location->longitud ?? -70.6693,
        ];

        return view('showescort', compact('usuarioPublicate', 'ciudades', 'ubicacion'));
    }

    public function showFavorites()
    {
        try {
            // Verificar autenticación
            $user = auth()->user();
            if (!$user) {
                Log::info('showFavorites: Usuario no autenticado');
                return redirect()->route('login');
            }
    
            // Obtener favoritos con paginación
            $favorites = Favorite::with(['usuarioPublicate' => function ($query) {
                $query->select([
                    'id',
                    'fantasia',
                    'email',
                    'nombre',
                    'telefono',
                    'ubicacion',
                    'edad',
                    'color_ojos',
                    'altura',
                    'peso',
                    'disponibilidad',
                    'servicios',
                    'servicios_adicionales',
                    'fotos',
                    'cuentanos',
                    'estadop',
                    'categorias',
                    'posicion',
                    'precio',
                    'nacionalidad',
                    'atributos',
                    'foto_positions'
                ]);
            }])
                ->where('user_id', $user->id)
                ->paginate(12);
    
            // Obtener ciudades
            $ciudades = Ciudad::all();
            $sectores = Sector::all();
            $nacionalidades = Nacionalidad::all();
    
            // Agrupar las ciudades por zona
            $ciudadesPorZona = $ciudades->groupBy('zona');
    
            // Obtener todas las tarjetas
            $tarjetas = Tarjeta::all();
    
            // Obtener los meta datos específicos para 'favorites'
            $meta = MetaTag::where('page', 'favoritos')->first();
    
            // Si no existe un registro de meta para favorites, creamos uno vacío con valores por defecto
            if (!$meta) {
                $meta = new MetaTag([
                    'page' => 'favorites',
                    'meta_title' => 'OnlyEscorts',
                    'meta_description' => 'Descubre los mejores favoritos en OnlyEscorts.',
                    'meta_keywords' => 'escorts, favoritos, OnlyEscorts',
                    'meta_robots' => 'index, follow',
                    'canonical_url' => url()->current(),
                ]);
            }
    
            // Definir datos adicionales para la vista
            $seoText = [
                'title' => $meta->meta_title ?? 'OnlyEscorts',
                'description' => $meta->meta_description ?? '',
            ];
    
            // Retornar vista con array_merge para combinar los datos
            return view('showfavorites', array_merge([
                'nacionalidades' => $nacionalidades,
                'sectores' => $sectores,
                'favorites' => $favorites,
                'ciudades' => $ciudades,
                'ciudadesPorZona' => $ciudadesPorZona,
                'tarjetas' => $tarjetas,
                'meta' => $meta,
                'totalOnline' => $favorites->count(), // Ejemplo de otro dato adicional
                'now' => \Carbon\Carbon::now()
            ], $seoText));
        } catch (\Exception $e) {
            Log::error('showFavorites: Error general', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
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
        try {
            Log::info('Iniciando actualización de foto de perfil', [
                'user_id' => auth()->id(),
                'has_file' => $request->hasFile('foto')
            ]);

            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:100000'
            ]);

            $usuario = auth()->user();

            if ($request->hasFile('foto')) {
                Log::info('Procesando archivo de foto', [
                    'original_name' => $request->file('foto')->getClientOriginalName(),
                    'size' => $request->file('foto')->getSize(),
                    'mime_type' => $request->file('foto')->getMimeType()
                ]);

                // Si existe una foto anterior, eliminarla
                if ($usuario->foto) {
                    Log::info('Eliminando foto anterior', ['path' => $usuario->foto]);
                    Storage::disk('public')->delete($usuario->foto);
                }

                // Guardar nueva foto
                $imagenPath = $request->file('foto')->store('profile_photos', 'public');

                Log::info('Nueva foto guardada', ['path' => $imagenPath]);

                // Actualizar usuario
                $usuario->foto = $imagenPath;
                $saved = $usuario->save();

                Log::info('Resultado de actualización de foto', [
                    'success' => $saved,
                    'user' => $usuario->toArray()
                ]);

                if (!$saved) {
                    throw new \Exception('No se pudo guardar la foto en la base de datos');
                }

                return redirect()
                    ->route('admin.profile')
                    ->with('success', 'Foto de perfil actualizada correctamente');
            }

            throw new \Exception('No se recibió ningún archivo');
        } catch (\Exception $e) {
            Log::error('Error actualizando foto de perfil', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()
                ->route('admin.profile')
                ->with('error', 'Error al actualizar la foto de perfil: ' . $e->getMessage());
        }
    }

    public function crearEstado(Request $request)
    {
        Log::info('Iniciando creación de estado', [
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        try {
            // Verificar autenticación
            if (!auth()->check()) {
                Log::error('Usuario no autenticado intentando crear estado');
                return redirect()->back()->with('error', 'Debes iniciar sesión');
            }

            // Log detallado del usuario
            Log::info('Datos del usuario actual', [
                'user_id' => auth()->id(),
                'rol' => auth()->user()->rol,
                'email' => auth()->user()->email
            ]);

            // Verificar rol
            if (auth()->user()->rol != 2) {
                Log::warning('Intento de crear estado por usuario no autorizado', [
                    'user_id' => auth()->id(),
                    'rol' => auth()->user()->rol,
                    'expected_rol' => 2
                ]);
                return redirect()->back()->with('error', 'No tienes permiso para crear estados');
            }

            // Log de archivos recibidos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $index => $file) {
                    Log::info('Archivo recibido', [
                        'index' => $index,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension()
                    ]);
                }
            } else {
                Log::warning('No se recibieron archivos en la solicitud');
            }

            // Validación con logging detallado
            try {
                $validator = \Validator::make($request->all(), [
                    'fotos.*' => 'required|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:3145728'
                ]);

                if ($validator->fails()) {
                    Log::error('Error de validación', [
                        'errors' => $validator->errors()->toArray()
                    ]);
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            } catch (\Exception $e) {
                Log::error('Error en la validación', [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]);
                return redirect()->back()->with('error', 'Error en la validación de archivos');
            }

            $user = auth()->user();

            // Log de búsqueda de usuario publicate
            Log::info('Buscando usuario publicate', [
                'user_id' => $user->id
            ]);

            $usuarioPublicate = $this->encontrarUsuarioPublicate($user);

            if (!$usuarioPublicate) {
                Log::error('No se encontró usuario publicate', [
                    'user_id' => $user->id
                ]);
                return redirect()->back()->with('error', 'No se encontró tu perfil de publicación.');
            }

            Log::info('Usuario publicate encontrado', [
                'usuario_publicate_id' => $usuarioPublicate->id
            ]);

            if ($request->hasFile('fotos')) {
                try {
                    // Log antes de procesar archivos
                    Log::info('Iniciando procesamiento de archivos multimedia');

                    $mediaFiles = $this->procesarArchivosMultimedia($request->file('fotos'));

                    // Log después de procesar archivos
                    Log::info('Archivos multimedia procesados', [
                        'processed_files' => $mediaFiles
                    ]);

                    // Log antes de crear el estado
                    Log::info('Intentando crear estado en la base de datos', [
                        'user_id' => $user->id,
                        'usuarios_publicate_id' => $usuarioPublicate->id,
                        'media_files' => $mediaFiles
                    ]);

                    $estado = Estado::create([
                        'user_id' => $user->id,
                        'usuarios_publicate_id' => $usuarioPublicate->id,
                        'fotos' => json_encode($mediaFiles)
                    ]);

                    Log::info('Estado creado exitosamente', [
                        'estado_id' => $estado->id,
                        'estado_data' => $estado->toArray(),
                        'archivos' => $mediaFiles
                    ]);

                    return redirect()->back()->with('success', 'Estado creado correctamente');
                } catch (\Exception $e) {
                    Log::error('Error al procesar archivos o crear estado', [
                        'error' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->back()->with('error', 'Error al procesar los archivos multimedia');
                }
            }

            Log::warning('No se proporcionaron archivos multimedia');
            return redirect()->back()->with('error', 'No se han proporcionado archivos multimedia');
        } catch (\Exception $e) {
            Log::error('Error general al crear estado', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
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

    // Método que ejecuta el comando delete-expired
    public function ejecutarEliminarEstadosExpirados()
    {
        try {
            // Ejecutar el comando de Artisan
            Artisan::call('estados:delete-expired');

            // Registrar en los logs que se ejecutó correctamente
            Log::info('El comando delete-expired ejecutado exitosamente.');

            return response()->json(['message' => 'Estados expirados eliminados correctamente.']);
        } catch (\Exception $e) {
            // Capturar cualquier error y registrar en el log
            Log::error('Error al ejecutar el comando delete-expired: ' . $e->getMessage());

            return response()->json(['error' => 'Hubo un error al eliminar los estados expirados'], 500);
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

    // En PerfilController.php
    public function toggleFavorite($id)
    {
        try {
            $user = auth()->user();
            $usuarioPublicate = UsuarioPublicate::findOrFail($id);

            $favorite = Favorite::where([
                'user_id' => $user->id,
                'usuario_publicate_id' => $id
            ])->first();

            if ($favorite) {
                $favorite->delete();
                $status = 'removed';
            } else {
                Favorite::create([
                    'user_id' => $user->id,
                    'usuario_publicate_id' => $id
                ]);
                $status = 'added';
            }

            Log::info('Favorito actualizado', [
                'user_id' => $user->id,
                'usuario_publicate_id' => $id,
                'action' => $status
            ]);

            return response()->json(['status' => $status]);
        } catch (\Exception $e) {
            Log::error('Error en toggle favorito', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'usuario_publicate_id' => $id
            ]);

            return response()->json(['error' => 'Error al procesar la solicitud'], 500);
        }
    }

    // Método para listar favoritos
    public function myFavorites()
    {
        $favorites = auth()->user()->favorites()
            ->with('usuarioPublicate')
            ->get();

        return view('favorites.index', compact('favorites'));
    }
}
