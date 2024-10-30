<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UsuarioPublicate;
use Illuminate\Support\Facades\File;


class PublicateController extends Controller
{
    public function showRegistrationForm()
    {
        return view('publicate');
    }


    public function store(Request $request)
{
    Log::info('todos los datos', $request->all());

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
            'servicios_adicionales' => 'nullable|array',
            'fotos.*' => 'nullable|image|max:2048', // Validación para cada imagen
            'about' => 'nullable|string',
            'declaration' => 'nullable|boolean',
        ]);

        // Crear el usuario primero para obtener el ID
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
            'servicios' => json_encode($validatedData['servicios'] ?? []),
            'servicios_adicionales' => json_encode($validatedData['servicios_adicionales'] ?? []),
            'cuentanos' => $validatedData['about'] ?? null,
        ]);

       // Procesar y guardar las imágenes
$nombresFotos = [];
if ($request->hasFile('fotos')) {
    Log::info('Archivos encontrados:', ['cantidad' => count($request->file('fotos'))]);
    
    foreach ($request->file('fotos') as $index => $foto) {
        // Crear un nombre único para la imagen
        $nombreArchivo = uniqid() . '_' . time() . '.' . $foto->getClientOriginalExtension();
        
        // Crear la carpeta si no existe
        $path = storage_path("app/public/chicas/{$usuario->id}");
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        try {
            // Guardar la imagen usando File::put
            $contenidoImagen = file_get_contents($foto->getRealPath());
            File::put($path . '/' . $nombreArchivo, $contenidoImagen);
            
            Log::info('Imagen guardada en:', [
                'path_completo' => $path . '/' . $nombreArchivo
            ]);
            
            // Agregar el nombre al array
            $nombresFotos[] = $nombreArchivo;
        } catch (\Exception $e) {
            Log::error('Error al guardar la imagen:', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    // Actualizar el usuario con los nombres de las fotos
    $usuario->update([
        'fotos' => json_encode($nombresFotos)
    ]);
}

        return redirect()->back()->with('success', 'Solicitud enviada con éxito.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Errores de validación:', $e->errors());
        return redirect()->back()->withErrors($e->errors());
    } catch (\Exception $e) {
        Log::error('Error al procesar la solicitud:', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Ocurrió un error al procesar la solicitud.');
    }
}
    
}
