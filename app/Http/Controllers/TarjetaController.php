<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TarjetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las tarjetas
        $tarjetas = Tarjeta::all();

        // Retornar la vista con las tarjetas
        return view('seo.tarjetas', compact('tarjetas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('seo.tarjetas-create'); // Asegúrate de crear esta vista
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'link' => 'required|url',
    ]);

    // Guardar la imagen en el sistema de archivos
    $imagePath = $request->file('imagen')->store('images', 'public');

    // Depuración
    Log::info('Ruta de la imagen guardada:', ['path' => $imagePath]);

    // Crear la nueva tarjeta
    Tarjeta::create([
        'titulo' => $request->titulo,
        'descripcion' => $request->descripcion,
        'link' => $request->link,
        'imagen' => $imagePath,
    ]);

    return redirect()->route('tarjetas.index')->with('success', 'Tarjeta creada con éxito.');
}

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tarjeta = Tarjeta::findOrFail($id); // Busca la tarjeta por su ID o lanza un error 404
        return view('seo.tarjetas-edit', compact('tarjeta')); // Pasa la tarjeta a la vista
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'link' => 'nullable|url',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
    
        $tarjeta = Tarjeta::findOrFail($id);
    
        // Actualizar los datos
        $tarjeta->titulo = $request->input('titulo');
        $tarjeta->descripcion = $request->input('descripcion');
        $tarjeta->link = $request->input('link');
    
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($tarjeta->imagen && Storage::exists('public/' . $tarjeta->imagen)) {
                Storage::delete('public/' . $tarjeta->imagen);
            }
    
            // Guardar la nueva imagen en el mismo lugar que en store
            $imagePath = $request->file('imagen')->store('images', 'public');
            $tarjeta->imagen = $imagePath;
        }
    
        $tarjeta->save();
    
        return redirect()->route('tarjetas.index')->with('success', 'Tarjeta actualizada con éxito.');
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscar la tarjeta por su ID
        $tarjeta = Tarjeta::findOrFail($id);
    
        // Eliminar la imagen asociada (si existe)
        if ($tarjeta->imagen && Storage::disk('public')->exists($tarjeta->imagen)) {
            Storage::disk('public')->delete($tarjeta->imagen);
        }
    
        // Eliminar la tarjeta de la base de datos
        $tarjeta->delete();
    
        // Redirigir con un mensaje de éxito
        return redirect()->route('tarjetas.index')->with('success', 'Tarjeta eliminada con éxito.');
    }
}
