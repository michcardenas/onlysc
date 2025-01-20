<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UsuarioPublicate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr; 
use App\Models\Ciudad;

class PublicateController extends Controller
{
    public function showRegistrationForm()
    {

        $ciudades = Ciudad::all();

        return view('publicate', compact('ciudades'));
    }

    public function store(Request $request)
    {
        Log::info('Iniciando proceso de registro', [
            'datos_recibidos' => Arr::except($request->all(), ['password', 'fotos'])
        ]);
        
        DB::beginTransaction();
        
        try {
            // Validar solo los campos necesarios para el registro inicial
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
                'cuentanos' => 'nullable|string',
            ]);

            Log::info('Datos validados correctamente', [
                'campos' => array_keys($validatedData)
            ]);

            // Preparar solo los datos iniciales necesarios
            $userData = [
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
                'cuentanos' => $validatedData['cuentanos'] ?? null,
                'estadop' => 0,  // Estado inicial pendiente
            ];

            Log::info('Intentando crear usuario con datos:', Arr::except($userData, ['password']));

            // Crear el usuario
            try {
                $usuario = UsuarioPublicate::create($userData);
                Log::info('Usuario creado exitosamente', ['id' => $usuario->id]);
            } catch (\Exception $e) {
                Log::error('Error al crear usuario en la base de datos', [
                    'mensaje' => $e->getMessage(),
                    'sql' => $e instanceof \Illuminate\Database\QueryException ? $e->getSql() : null,
                ]);
                throw $e;
            }

            // Procesar y guardar las imágenes
            if ($request->hasFile('fotos')) {
                $nombresFotos = [];
                foreach ($request->file('fotos') as $foto) {
                    $nombreArchivo = uniqid() . '_' . time() . '.' . $foto->getClientOriginalExtension();
                    $path = storage_path("app/public/chicas/{$usuario->id}");
                    
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0755, true);
                    }
                    
                    try {
                        $foto->move($path, $nombreArchivo);
                        $nombresFotos[] = $nombreArchivo;
                        Log::info('Foto guardada exitosamente', [
                            'nombre' => $nombreArchivo,
                            'ruta' => $path
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error al guardar foto', [
                            'error' => $e->getMessage(),
                            'ruta' => $path
                        ]);
                        throw $e;
                    }
                }
                
                // Actualizar el usuario con las fotos
                try {
                    $usuario->fotos = json_encode($nombresFotos);
                    $usuario->save();
                    Log::info('Fotos actualizadas en la base de datos', [
                        'cantidad' => count($nombresFotos)
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al actualizar fotos en la base de datos', [
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            DB::commit();
            Log::info('Transacción completada exitosamente');
            
            return redirect()->back()->with('success', 'Tu perfil ha sido creado y está pendiente de aprobación.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación:', [
                'errores' => $e->errors()
            ]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error general en el proceso:', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Ocurrió un error al procesar tu registro. Por favor, intenta nuevamente.')
                ->withInput();
        }
    }
}