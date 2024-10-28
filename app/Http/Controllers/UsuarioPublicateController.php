<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioPublicate;
use Illuminate\Support\Facades\File;


class UsuarioPublicateController extends Controller
{
    public function edit($id)
    {
        // Obtener la información del usuario a partir del ID
        $usuario = UsuarioPublicate::findOrFail($id);

        // Pasar los datos del usuario a la vista de edición
        return view('admin.edit', compact('usuario'));
    }
    public function update(Request $request, $id)
    {
        $usuario = UsuarioPublicate::findOrFail($id);
    
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
            'servicios' => 'nullable|string',
            'servicios_adicionales' => 'nullable|string',
            'cuentanos' => 'nullable|string',
            'estadop' => 'required|integer|in:0,1',
            'categorias' => 'required|string|in:deluxe,premium,VIP,masajes', // Validación para la categoría
            'fotos.*' => 'nullable|image|max:2048', // Validación para cada imagen
        ]);
    
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
            'servicios' => $request->servicios,
            'servicios_adicionales' => $request->servicios_adicionales,
            'cuentanos' => $request->cuentanos,
            'estadop' => $request->estadop,
            'categorias' => $request->categorias, // Almacenar la categoría seleccionada
        ]);
    
        // Procesar y guardar las nuevas imágenes
        $nombresFotos = json_decode($usuario->fotos, true) ?: []; // Obtener fotos actuales
    
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                // Crear un nombre único para la imagen
                $nombreArchivo = uniqid() . '_' . time() . '.' . $foto->getClientOriginalExtension();
                $path = storage_path("app/public/chicas/{$usuario->id}");
    
                // Crear la carpeta si no existe
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }
    
                // Guardar la imagen en el almacenamiento
                $foto->move($path, $nombreArchivo);
    
                // Agregar el nombre de la imagen al array
                $nombresFotos[] = $nombreArchivo;
            }
        }
    
        // Actualizar el campo 'fotos' con los nombres de las imágenes
        $usuario->update([
            'fotos' => json_encode($nombresFotos),
        ]);
    
        // Redirigir de vuelta a la vista del panel de control con un mensaje de éxito
        return redirect()->route('panel_control')->with('success', 'Usuario actualizado correctamente.');
    }
    
    

}
