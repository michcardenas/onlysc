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
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Buscar la ciudad por su ID y actualizar
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->update($request->only('nombre'));

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
            ]);

            // Crear la nueva ciudad
            Ciudad::create([
                'nombre' => $request->input('nombre'),
            ]);

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
