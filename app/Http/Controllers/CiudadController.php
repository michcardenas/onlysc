<?php

namespace App\Http\Controllers;

use App\Models\Ciudad; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


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
            'zona' => 'nullable|string|max:255', // Validar zona como string opcional
            'posicion' => [
                'nullable',
                'integer',
            ],
        ], [
            'url.regex' => 'El campo URL no puede contener espacios, puntos, comas ni barras (/).',
        ]);
    
        // Buscar la ciudad por su ID
        $ciudad = Ciudad::findOrFail($id);
    
        // Si se actualiza la posición, verificar conflictos
        if ($request->filled('posicion') && $request->filled('zona')) {
            $posicionExistente = Ciudad::where('zona', $request->zona)
                ->where('posicion', $request->posicion)
                ->where('id', '!=', $id) // Excluir la ciudad actual
                ->first();
    
            if ($posicionExistente) {
                // Si ya existe, mueve el registro conflictivo al final
                $ultimaPosicion = Ciudad::where('zona', $request->zona)
                    ->max('posicion');
    
                $posicionExistente->update(['posicion' => $ultimaPosicion + 1]);
            }
        }
    
        // Actualizar los campos permitidos
        $ciudad->update($request->only('nombre', 'url', 'zona', 'posicion'));
    
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
                'zona' => 'nullable|string|max:255', // Validar zona como string opcional
                'posicion' => [
                    'nullable',
                    'integer',
                    Rule::unique('ciudades')->where(function ($query) use ($request) {
                        return $query->where('zona', $request->zona); // Validar unicidad de posicion por zona
                    }),
                ],
            ], [
                // Mensajes personalizados
                'url.regex' => 'El campo URL no puede contener espacios, puntos, comas ni barras (/).',
                'posicion.unique' => 'La posición ya está en uso en la misma zona.',
            ]);
        
            // Crear la nueva ciudad
            Ciudad::create($request->only('nombre', 'url', 'zona', 'posicion'));
        
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
