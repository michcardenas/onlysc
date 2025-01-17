<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioPublicate;
use App\Models\EscortLocation;
use App\Models\Disponibilidad;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UsuarioPublicateController extends Controller
{
    protected $disponibilidadController;

    public function __construct(DisponibilidadController $disponibilidadController)
    {
        $this->disponibilidadController = $disponibilidadController;
    }

    private function normalizeString($string)
    {
        if (!is_string($string)) return '';

        // Convertir a minúsculas y remover tildes
        $string = mb_strtolower($string, 'UTF-8');
        $string = preg_replace('/[áàãâä]/u', 'a', $string);
        $string = preg_replace('/[éèêë]/u', 'e', $string);
        $string = preg_replace('/[íìîï]/u', 'i', $string);
        $string = preg_replace('/[óòõôö]/u', 'o', $string);
        $string = preg_replace('/[úùûü]/u', 'u', $string);
        $string = preg_replace('/[ýÿ]/u', 'y', $string);
        $string = preg_replace('/[ñ]/u', 'n', $string);

        // Remover caracteres especiales y espacios múltiples
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = trim($string);

        return $string;
    }

    public function validateFantasia(Request $request)
    {
        $exists = UsuarioPublicate::where('ubicacion', $request->ubicacion)
            ->where('id', '!=', $request->userId)
            ->where(DB::raw('LOWER(fantasia)'), 'LIKE', mb_strtolower($request->fantasia))
            ->exists();

        return response()->json(['valid' => !$exists]);
    }

    public function edit($id)
    {
        try {
            // Obtener el usuario
            $usuario = UsuarioPublicate::findOrFail($id);

            // Obtener la disponibilidad
            $disponibilidad = Disponibilidad::where('publicate_id', $id)->get();

            // Obtener las ciudades
            $ciudades = DB::table('ciudades')->orderBy('nombre')->get();

            // Preparar los datos para la vista
            $diasDisponibles = $disponibilidad->pluck('dia')->toArray();
            $horarios = [];

            foreach ($disponibilidad as $disp) {
                $horarios[$disp->dia] = [
                    'desde' => $disp->hora_desde,
                    'hasta' => $disp->hora_hasta
                ];
            }

            $usuarioAutenticado = auth()->user();

            // Obtener las posiciones de las fotos
            $positions = json_decode($usuario->foto_positions, true) ?? [];

            // Si hay una foto destacada pero no tiene posición, establecer 'center' por defecto
            if (!empty(json_decode($usuario->fotos, true))) {
                $fotoDestacada = json_decode($usuario->fotos, true)[0];
                if (!isset($positions[$fotoDestacada])) {
                    $positions[$fotoDestacada] = 'center';
                }
            }

            // Pasar a la vista incluyendo las posiciones
            return view('admin.edit', compact('usuario', 'diasDisponibles', 'horarios', 'ciudades', 'positions', "usuarioAutenticado"));
        } catch (\Exception $e) {
            Log::error("Error al cargar el formulario de edición: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info("Iniciando actualización del usuario con ID: $id");
    
            $usuario = UsuarioPublicate::findOrFail($id);
            Log::info("Usuario encontrado: {$usuario->id}");
    
            // Validación personalizada para nombre de fantasía único por ciudad
            $normalizedFantasia = $this->normalizeString($request->fantasia);
    
            $fantasiaExists = UsuarioPublicate::where(function ($query) use ($normalizedFantasia, $request) {
                $query->whereRaw('LOWER(fantasia) COLLATE utf8mb4_unicode_ci = ?', [mb_strtolower($request->fantasia)])
                    ->orWhereRaw('? = ?', [$normalizedFantasia, $this->normalizeString(DB::raw('fantasia'))]);
            })
                ->where('ubicacion', $request->ubicacion)
                ->where('id', '!=', $id)
                ->exists();
    
            if ($fantasiaExists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['fantasia' => 'Ya existe una persona con este nombre de fantasía en esta ciudad.']);
            }
    
            // Verificar si está marcado como Chica del Mes
            if ($request->has('chica_del_mes')) {
                $existingChicaDelMes = UsuarioPublicate::where('ubicacion', $request->ubicacion)
                    ->where('estadop', 3)
                    ->where('id', '!=', $id)
                    ->first();
    
                if ($existingChicaDelMes) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['chica_del_mes' => 'Ya existe una Chica del Mes en esta ciudad.']);
                }
    
                $request->merge(['estadop' => 3]);
            }
    
            // Agregar validaciones para los campos del mapa
            $request->validate([
                'fantasia' => 'required|string|max:255',
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telefono' => 'nullable|string|max:20',
                'ubicacion' => 'required|string|exists:ciudades,nombre',
                'edad' => 'required|integer|min:18|max:100',
                'color_ojos' => 'nullable|string|max:50',
                'altura' => 'nullable|numeric|min:0|max:300',
                'peso' => 'nullable|numeric|min:0|max:300',
                'disponibilidad' => 'nullable|string',
                'servicios' => 'nullable|array',
                'servicios.*' => 'string',
                'servicios_adicionales' => 'nullable|array',
                'servicios_adicionales.*' => 'string',
                'atributos' => 'nullable|array',
                'atributos.*' => 'string',
                'nacionalidad' => 'required|string|max:100',
                'cuentanos' => 'nullable|string',
                'estadop' => 'required|integer|in:0,1,3',
                'categorias' => 'required|string|in:deluxe,premium,VIP,masajes',
                'posicion' => 'nullable|integer|unique:usuarios_publicate,posicion,' . $usuario->id,
                'precio' => 'nullable|numeric|min:0',
                'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'foto_destacada' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,webm|max:2048',
                'dias_disponibles' => 'array',
                'dias_disponibles.*' => 'string',
                'horario' => 'array',
                'horario.*' => 'array',
                // Validaciones para el mapa
                'latitud' => 'required|numeric|between:-90,90',
                'longitud' => 'required|numeric|between:-180,180',
                'direccion_mapa' => 'required|string|max:255',
            ]);
    
            Log::info("Validación completada para el usuario con ID: $id");
    
            // Iniciar transacción de base de datos
            DB::beginTransaction();
    
            try {
                // Actualizar los datos del usuario
                $usuario->update([
                    'fantasia' => $request->fantasia,
                    'nombre' => $request->nombre,
                    'email' => $request->email,
                    'telefono' => $request->telefono,
                    'ubicacion' => $request->ubicacion,
                    'edad' => $request->edad,
                    'color_ojos' => $request->color_ojos,
                    'altura' => $request->altura,
                    'peso' => $request->peso,
                    'disponibilidad' => $request->disponibilidad,
                    'servicios' => json_encode($request->servicios ?? []),
                    'servicios_adicionales' => json_encode($request->servicios_adicionales ?? []),
                    'atributos' => json_encode($request->atributos ?? []),
                    'nacionalidad' => $request->nacionalidad,
                    'cuentanos' => $request->cuentanos,
                    'estadop' => $request->has('chica_del_mes') ? 3 : $request->estadop,
                    'categorias' => $request->categorias,
                    'posicion' => $request->posicion,
                    'precio' => $request->precio,
                ]);
    
                // Actualizar o crear la ubicación en el mapa
                EscortLocation::updateOrCreate(
                    ['usuario_publicate_id' => $usuario->id],
                    [
                        'direccion' => $request->direccion_mapa,
                        'ciudad' => $request->ubicacion,
                        'latitud' => $request->latitud,
                        'longitud' => $request->longitud,
                        'is_approximate' => true
                    ]
                );
    
                Log::info("Datos básicos y ubicación del usuario actualizados");
    
                // Actualizar disponibilidad usando el controlador dedicado
                if ($request->has('dias_disponibles') && $request->has('horario')) {
                    $this->disponibilidadController->updateDisponibilidad(
                        $usuario->id,
                        $request->dias_disponibles,
                        $request->horario,
                        $request->fulltime ?? []
                    );
                }

                // Procesar fotos
                $nombresFotos = json_decode($usuario->fotos, true) ?: [];
                Log::info('Estado inicial de fotos:', ['fotos' => $nombresFotos]);

                // Manejar foto destacada
                if ($request->hasFile('foto_destacada')) {
                    Log::info('Procesando foto destacada');
                    
                    $foto = $request->file('foto_destacada');
                    $nombreArchivo = $this->processImage($foto, $usuario->id);

                    if ($nombreArchivo) {
                        Log::info('Nueva foto destacada procesada:', ['nombre' => $nombreArchivo]);

                        // Si hay fotos existentes, manejar la foto destacada anterior
                        if (!empty($nombresFotos)) {
                            $fotoDestacadaAnterior = $nombresFotos[0];

                            // Eliminar el archivo físico de la foto destacada anterior
                            $pathAnterior = storage_path("app/public/chicas/{$usuario->id}/{$fotoDestacadaAnterior}");
                            if (File::exists($pathAnterior)) {
                                File::delete($pathAnterior);
                                Log::info('Foto destacada anterior eliminada:', ['nombre' => $fotoDestacadaAnterior]);
                            }

                            // Eliminar la referencia de la foto anterior del array
                            $nombresFotos = array_diff($nombresFotos, [$fotoDestacadaAnterior]);
                        }

                        // Colocar la nueva foto destacada al inicio del array
                        $nombresFotos = array_values($nombresFotos); // Reindexar el array
                        array_unshift($nombresFotos, $nombreArchivo);
                    } else {
                        Log::error('Error al procesar la foto destacada');
                        throw new \Exception('No se pudo procesar la foto destacada');
                    }
                }

                // Procesar fotos adicionales
                if ($request->hasFile('fotos')) {
                    foreach ($request->file('fotos') as $foto) {
                        $nombreArchivo = $this->processImage($foto, $usuario->id);
                        
                        if ($nombreArchivo) {
                            $nombresFotos[] = $nombreArchivo;
                            Log::info('Nueva foto adicional procesada:', ['nombre' => $nombreArchivo]);
                        } else {
                            Log::error('Error al procesar foto adicional');
                            continue;
                        }
                    }
                }

                Log::info('Estado final de fotos:', ['fotos' => $nombresFotos]);

                // Asegurar que el array esté correctamente indexado antes de guardarlo
                $nombresFotos = array_values(array_unique($nombresFotos));

                // Actualizar las fotos en la base de datos
                $usuario->update(['fotos' => json_encode($nombresFotos)]);

                // Crear usuario en tabla users si está activo o es chica del mes
                if ($usuario->estadop == 1 || $usuario->estadop == 3) {
                    $user = User::updateOrCreate(
                        ['email' => $usuario->email],
                        [
                            'name' => $usuario->nombre,
                            'email_verified_at' => now(),
                            'password' => bcrypt($usuario->nombre),
                            'remember_token' => Str::random(10),
                            'rol' => 2,
                        ]
                    );

                    try {
                        Notification::send($user, new UserCreatedNotification($usuario->nombre, $usuario->email));
                        Log::info("Notificación enviada a {$user->email}");
                    } catch (\Exception $e) {
                        Log::error("Error al enviar la notificación: " . $e->getMessage());
                    }
                }

                DB::commit();

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Usuario actualizado correctamente',
                        'fotos' => $nombresFotos // Devolver el array de fotos actualizado
                    ]);
                }

                $message = $request->has('chica_del_mes')
                    ? 'Usuario actualizado correctamente y establecido como Chica del Mes.'
                    : 'Usuario actualizado correctamente.';

                return redirect()->route('panel_control')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error en la actualización: " . $e->getMessage(), [
                    'usuario_id' => $id,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("Error general: " . $e->getMessage(), [
                'usuario_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar las fotos: ' . $e->getMessage()
                ]);
            }

            return redirect()->route('panel_control')
                ->with('error', 'Ocurrió un error al actualizar el usuario. Inténtalo de nuevo.');
        }
    }

    public function eliminarFoto(Request $request)
    {
        try {
            $usuario = UsuarioPublicate::findOrFail($request->usuario_id);
            $fotos = json_decode($usuario->fotos, true) ?: [];
            $foto = $request->foto;
            $esDestacada = $request->es_destacada ?? false;

            // Si es la foto destacada y hay más fotos, reorganizar el array
            if ($esDestacada && count($fotos) > 1) {
                if (($key = array_search($foto, $fotos)) !== false) {
                    unset($fotos[$key]);
                    $fotos = array_values($fotos); // Reindexar el array
                }
            } else {
                // Eliminar foto normal
                if (($key = array_search($foto, $fotos)) !== false) {
                    unset($fotos[$key]);
                    $fotos = array_values($fotos); // Reindexar el array
                }
            }

            // Eliminar el archivo físico
            $path = storage_path("app/public/chicas/{$request->usuario_id}/{$foto}");
            if (File::exists($path)) {
                File::delete($path);
            }

            // Actualizar la base de datos
            $usuario->update(['fotos' => json_encode($fotos)]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error al eliminar imagen: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen'
            ]);
        }
    }

    public function actualizarPosicionFoto(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'foto' => 'required',
                'position' => 'required|in:left,center,right'
            ]);

            $usuario = UsuarioPublicate::findOrFail($request->user_id);

            // Decodificar las posiciones actuales o crear un array vacío si no existen
            $positions = json_decode($usuario->foto_positions, true) ?? [];

            // Actualizar la posición para esta foto específica
            $positions[$request->foto] = $request->position;

            // Guardar las posiciones actualizadas
            $usuario->update(['foto_positions' => json_encode($positions)]);

            // Si todo salió bien, devolver respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Posición actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar posición de foto: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la posición: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function processImage($image, $userId) {
        try {
            $manager = new ImageManager(new Driver());
            
            // Generar nombre único
            $nombreArchivo = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $nombreThumb = 'thumb_' . $nombreArchivo;
            $path = storage_path("app/public/chicas/{$userId}");
    
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
    
            // Guardar versión original
            $image->move($path, $nombreArchivo);
            
            // Crear y guardar thumbnail
            $img = $manager->read($path . '/' . $nombreArchivo);
            
            // Dimensiones originales
            $originalWidth = $img->width();
            $originalHeight = $img->height();
            
            // Dimensiones objetivo (proporción 294:204 ≈ 1.44:1)
            $targetWidth = 588;  // Doble del ancho deseado
            $targetHeight = 408; // Doble del alto deseado
            
            // Realizar el cover sin el closure
            $img->cover($targetWidth, $targetHeight);
    
            // Usar el encoder con alta calidad
            $encoder = new \Intervention\Image\Encoders\JpegEncoder(85);
            $encodedImage = $img->encode($encoder);
            file_put_contents($path . '/' . $nombreThumb, $encodedImage);
            
            return $nombreArchivo;
        } catch (\Exception $e) {
            Log::error('Error al procesar imagen: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }

    public function toggleImageBlock(Request $request)
    {
        try {
            $usuario = UsuarioPublicate::findOrFail($request->user_id);
            
            // Inicializar blocked_images como array si es null
            $blockedImages = json_decode($usuario->blocked_images, true) ?? [];
            
            if ($request->blocked) {
                // Agregar imagen al array si no existe
                if (!in_array($request->image, $blockedImages)) {
                    $blockedImages[] = $request->image;
                }
            } else {
                // Remover imagen del array si existe
                $blockedImages = array_values(array_filter($blockedImages, function($img) use ($request) {
                    return $img !== $request->image;
                }));
            }
            
            $usuario->blocked_images = json_encode($blockedImages);
            $usuario->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Estado de bloqueo actualizado',
                'blocked_images' => $blockedImages,
                'estado_blocked' => $request->blocked, // Para debug
                'imagen' => $request->image // Para debug
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en toggleImageBlock: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

}
