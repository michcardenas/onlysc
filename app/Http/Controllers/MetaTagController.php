<?php

namespace App\Http\Controllers;

use App\Models\MetaTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MetaTagController extends Controller
{
    public function update(Request $request, $page)
    {
        \Log::info('Request recibido:', $request->all());
        \Log::info('Página:', ['page' => $page]);
    
        try {
            // Validación de datos
            $validated = $request->validate([
                'meta_title' => 'required|max:255',
                'meta_description' => 'required',
                'meta_keywords' => 'nullable|max:255',
                'canonical_url' => 'nullable|url|max:255',
                'meta_robots' => 'required',
                'heading_h1' => 'nullable|max:255',
                'heading_h2' => 'nullable|max:255',
                'heading_h2_secondary' => 'nullable|max:255',
                'additional_text' => 'nullable',
                'additional_text_more' => 'nullable',
                'texto_zonas' => 'nullable',
                'titulo_tarjetas' => 'nullable|max:255',
                'fondo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'marca_agua' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Nueva validación
                'texto_zonas_centro' => 'nullable',
                'texto_zonas_sur' => 'nullable'
            ]);
    
            \Log::info('Datos validados:', $validated);
    
            // Remover archivos de los datos validados
            $imageFile = $request->file('fondo');
            $marcaAguaFile = $request->file('marca_agua'); // Nuevo
            unset($validated['fondo']);
            unset($validated['marca_agua']); // Nuevo
    
            // Buscar el registro existente o crear uno nuevo
            $meta = MetaTag::firstOrNew(['page' => $page]);
    
            // Procesar la imagen de fondo si se proporcionó una nueva
            if ($imageFile) {
                if ($meta->fondo && Storage::exists('public/' . $meta->fondo)) {
                    Storage::delete('public/' . $meta->fondo);
                }
                $imagePath = $imageFile->store('images/fondos', 'public');
                $validated['fondo'] = $imagePath;
            }
    
            // Procesar la marca de agua si se proporcionó una nueva
            if ($marcaAguaFile) {
                if ($meta->marca_agua && Storage::exists('public/' . $meta->marca_agua)) {
                    Storage::delete('public/' . $meta->marca_agua);
                }
                $marcaAguaPath = $marcaAguaFile->store('images/marca_agua', 'public');
                $validated['marca_agua'] = $marcaAguaPath;
            }
    
            // Actualizar los datos
            $meta->fill($validated);
            $meta->save();
    
            \Log::info('Registro guardado:', $meta->toArray());
    
            return redirect()->back()->with('success', 'Etiquetas Meta actualizadas correctamente.');
    
        } catch (\Exception $e) {
            \Log::error('Error en actualización:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return redirect()->back()
                ->with('error', 'Error al guardar las etiquetas meta: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function getMetaTagByCiudad($ciudad_id)
{
    $metatag = MetaTag::where('page', 'inicio-' . $ciudad_id)->first();
    return response()->json($metatag);
}
}