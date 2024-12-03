<?php

namespace App\Http\Controllers;

use App\Models\Ciudad; 
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    public function index()
    {
        $ciudades = Ciudad::all();

        return view('ciudades.index', compact('ciudades'));
    }
    public function edit($id)
    {
        // Buscar la ciudad por su ID
        $ciudad = Ciudad::findOrFail($id);

        // Retornar la vista de edición con los datos de la ciudad
        return view('ciudades.edit', compact('ciudad'));
    }

    /**
     * Actualiza los datos de la ciudad en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'url' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^\s.,\/]+$/', 
            ],
        ], [
            // Mensajes personalizados para la validación de 'regex'
            'url.regex' => 'El campo URL no puede contener espacios, puntos, comas ni barras (/).',
        ]);
    
        // Buscar la ciudad por su ID y actualizar
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->update($request->only('nombre', 'url'));
    
        // Redirigir con un mensaje de éxito
        return redirect()->route('ciudades.index')->with('success', 'Ciudad actualizada con éxito.');
    }
    
    

        public function create()
        {
            // Retorna la vista para agregar una nueva ciudad
            return view('ciudades.create');
        }

        public function store(Request $request)
        {
            // Validar los datos del formulario
            $request->validate([
                'nombre' => 'required|string|max:255',
                'url' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[^\s.,\/]+$/', // No permite espacios, puntos, comas ni barras
                ],
            ], [
                // Mensajes personalizados para la validación de 'regex'
                'url.regex' => 'El campo URL no puede contener espacios, puntos, comas ni barras (/).',
            ]);
        
            // Crear la nueva ciudad
            Ciudad::create($request->only('nombre', 'url'));
        
            // Redirigir a la lista de ciudades con un mensaje de éxito
            return redirect()->route('ciudades.index')->with('success', 'Ciudad agregada con éxito.');
        }
        
        
        
        public function destroy($id)
        {
            // Buscar la ciudad por su ID
            $ciudad = Ciudad::findOrFail($id);
        
            // Eliminar la ciudad
            $ciudad->delete();
        
            // Redirigir a la lista de ciudades con un mensaje de éxito
            return redirect()->route('ciudades.index')->with('success', 'Ciudad eliminada con éxito.');
        }
        

}
