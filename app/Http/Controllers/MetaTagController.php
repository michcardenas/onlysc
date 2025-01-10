<?php

namespace App\Http\Controllers;

use App\Models\MetaTag;
use Illuminate\Http\Request;

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
                'additional_text' => 'nullable',
            ]);
    
            \Log::info('Datos validados:', $validated);
    
            // Buscar o crear el registro
            $meta = MetaTag::updateOrCreate(
                ['page' => $page],
                $validated
            );
    
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