<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioPublicate;
use App\Models\Disponibilidad;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Añade esta línea


class UsuarioPublicateController extends Controller
{
    public function edit($id)
    {
        try {
            // Obtener el usuario
            $usuario = UsuarioPublicate::findOrFail($id);
            
            // Obtener la disponibilidad
            $disponibilidad = Disponibilidad::where('publicate_id', $id)->get();
            
            // Preparar los datos para la vista
            $diasDisponibles = $disponibilidad->pluck('dia')->toArray();
            $horarios = [];
            
            foreach($disponibilidad as $disp) {
                $horarios[$disp->dia] = [
                    'desde' => $disp->hora_desde,
                    'hasta' => $disp->hora_hasta
                ];
            }
            
            // Pasar a la vista
            return view('admin.edit', compact('usuario', 'diasDisponibles', 'horarios'));
            
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
    
            // Validar los datos del formulario
            $request->validate([
                'fantasia' => 'required|string|max:255',
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telefono' => 'nullable|string|max:20',
                'ubicacion' => 'required|string|max:255',
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
                'estadop' => 'required|integer|in:0,1',
                'categorias' => 'required|string|in:deluxe,premium,VIP,masajes',
                'posicion' => 'nullable|integer|unique:usuarios_publicate,posicion,' . $usuario->id,
                'precio' => 'nullable|numeric|min:0',
                'fotos.*' => 'nullable|image|max:2048',
                'dias_disponibles' => 'array',
                'dias_disponibles.*' => 'string',
                'horario' => 'array',
                'horario.*' => 'array',
            ]);
    
            Log::info("Validación completada para el usuario con ID: $id");
    
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
                'estadop' => $request->estadop,
                'categorias' => $request->categorias,
                'posicion' => $request->posicion,
                'precio' => $request->precio,
            ]);
    
            // Procesar fotos de forma separada
            DB::beginTransaction();
            
            try {
                $nombresFotos = json_decode($usuario->fotos, true) ?: [];
    
                if ($request->hasFile('fotos')) {
                    foreach ($request->file('fotos') as $foto) {
                        $nombreArchivo = uniqid() . '_' . time() . '.' . $foto->getClientOriginalExtension();
                        $path = storage_path("app/public/chicas/{$usuario->id}");
    
                        if (!File::exists($path)) {
                            File::makeDirectory($path, 0755, true);
                        }
    
                        $foto->move($path, $nombreArchivo);
                        $nombresFotos[] = $nombreArchivo;
                    }
    
                    DB::reconnect();
                    $usuario->update([
                        'fotos' => json_encode($nombresFotos),
                    ]);
                }
    
                // Si el estado es activo, crear un usuario en la tabla users
                if ($usuario->estadop == 1) {
                    Log::info("El estado del usuario es activo, creando usuario en 'users'");
    
                    DB::reconnect();
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
    
                    Log::info("Usuario creado o actualizado en la tabla 'users' con email: {$user->email}");
    
                    try {
                        Notification::send($user, new UserCreatedNotification($usuario->nombre, $usuario->email));
                        Log::info("Notificación enviada a {$user->email}");
                    } catch (\Exception $e) {
                        Log::error("Error al enviar la notificación: " . $e->getMessage());
                    }
                } else {
                    Log::info("El estado del usuario no es activo, no se envió la notificación.");
                }
    
                DB::commit();
                return redirect()->route('panel_control')->with('success', 'Usuario actualizado correctamente.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Error: El usuario con ID $id no existe.");
            return redirect()->route('panel_control')->with('error', 'El usuario no existe.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Error de validación: " . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error general al actualizar el usuario: " . $e->getMessage());
            return redirect()->route('panel_control')
                ->with('error', 'Ocurrió un error al actualizar el usuario. Inténtalo de nuevo.');
        }
    }

    public function eliminarFoto(Request $request)
    {
        try {
            $usuario = UsuarioPublicate::findOrFail($request->usuario_id);
            $fotos = json_decode($usuario->fotos, true) ?: [];
            
            if (($key = array_search($request->foto, $fotos)) !== false) {
                // Eliminar el archivo físico
                $path = storage_path("app/public/chicas/{$request->usuario_id}/{$request->foto}");
                if (File::exists($path)) {
                    File::delete($path);
                }
                
                // Eliminar del array
                unset($fotos[$key]);
                
                // Actualizar la base de datos
                $usuario->update([
                    'fotos' => json_encode(array_values($fotos))
                ]);
                
                return response()->json(['success' => true]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la imagen'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error al eliminar imagen: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen'
            ]);
        }
    }
}

