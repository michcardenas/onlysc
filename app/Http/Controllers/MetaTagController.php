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
                'texto_zonas' => 'nullable', // Validación para texto_zonas
                'titulo_tarjetas' => 'nullable|max:255', // Validación para titulo_tarjetas
                'fondo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);
    
            \Log::info('Datos validados:', $validated);
    
            // Remover fondo de los datos validados ya que se procesará por separado
            $imageFile = $request->file('fondo');
            unset($validated['fondo']);
    
            // Buscar el registro existente o crear uno nuevo
            $meta = MetaTag::firstOrNew(['page' => $page]);
    
            // Procesar la imagen si se proporcionó una nueva
            if ($imageFile) {
                // Eliminar la imagen anterior si existe
                if ($meta->fondo && Storage::exists('public/' . $meta->fondo)) {
                    Storage::delete('public/' . $meta->fondo);
                }
    
                // Guardar la nueva imagen
                $imagePath = $imageFile->store('images/fondos', 'public');
                $validated['fondo'] = $imagePath;
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
}