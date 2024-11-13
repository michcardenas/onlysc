<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UsuarioPublicate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class PublicateController extends Controller
{
    public function showRegistrationForm()
    {
        return view('publicate');
    }

    public function store(Request $request)
    {
        Log::info('Iniciando proceso de registro');
        
        DB::beginTransaction();
        
        try {
            // Convertir el campo 'declaration' a booleano
            $request->merge([
                'declaration' => $request->has('declaration') ? true : false,
            ]);

            // Validar los datos del formulario
            $validatedData = $request->validate([
                'fantasia' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'nombre' => 'required|string|max:255',
                'password' => 'required|string|min:6',
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
                'fotos.*' => 'nullable|image|max:2048',
                'about' => 'nullable|string',
                'declaration' => 'nullable|boolean',
            ]);

            // Crear el usuario
            $usuario = UsuarioPublicate::create([
                'fantasia' => $validatedData['fantasia'],
                'email' => $validatedData['email'],
                'nombre' => $validatedData['nombre'],
                'password' => bcrypt($validatedData['password']),
                'telefono' => $validatedData['telefono'] ?? null,
                'ubicacion' => $validatedData['ubicacion'],
                'edad' => $validatedData['edad'],
                'color_ojos' => $validatedData['color_ojos'] ?? null,
                'altura' => $validatedData['altura'] ?? null,
                'peso' => $validatedData['peso'] ?? null,
                'disponibilidad' => $validatedData['disponibilidad'] ?? null,
                'servicios' => json_encode($request->servicios ?? []),
                'servicios_adicionales' => json_encode($request->servicios_adicionales ?? []),
                'cuentanos' => $validatedData['about'] ?? null,
                'estadop' => 0, // Estado inicial inactivo
            ]);

            // Procesar y guardar las imágenes
            $nombresFotos = [];
            if ($request->hasFile('fotos')) {
                Log::info('Procesando imágenes:', ['cantidad' => count($request->file('fotos'))]);
                
                foreach ($request->file('fotos') as $foto) {
                    $nombreArchivo = uniqid() . '_' . time() . '.' . $foto->getClientOriginalExtension();
                    $path = storage_path("app/public/chicas/{$usuario->id}");
                    
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0755, true);
                    }
                    
                    try {
                        $contenidoImagen = file_get_contents($foto->getRealPath());
                        File::put($path . '/' . $nombreArchivo, $contenidoImagen);
                        
                        Log::info('Imagen guardada:', ['ruta' => $path . '/' . $nombreArchivo]);
                        
                        $nombresFotos[] = $nombreArchivo;
                        
                        // Actualizar después de cada foto para evitar timeouts
                        DB::reconnect();
                        $usuario->update(['fotos' => json_encode($nombresFotos)]);
                        
                    } catch (\Exception $e) {
                        Log::error('Error al guardar imagen:', ['error' => $e->getMessage()]);
                        throw $e;
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Tu perfil ha sido creado y está pendiente de aprobación.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Errores de validación:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en el registro:', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Ocurrió un error al procesar tu registro. Por favor, intenta nuevamente.')
                ->withInput();
        }
    }
}