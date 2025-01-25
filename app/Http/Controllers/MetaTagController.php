<?php

namespace App\Http\Controllers;

use App\Models\MetaTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MetaTagController extends Controller
{
    private $servicios = ["Anal","Atencion a domicilio","Atencion en hoteles","Baile erotico","Besos","Cambio de rol","Departamento propio","Disfraces","Ducha erotica","Eventos y cenas","Eyaculacion cuerpo","Eyaculacion facial","Hetero","Juguetes","Lesbico","Lluvia dorada","Masaje erotico","Masaje prostatico","Masaje tantrico","Masaje thai","Masajes con final feliz","Masajes desnudos","Masajes eroticos","Masajes para hombres","Masajes sensitivos","Masajes sexuales","Masturbacion rusa","Oral americana","Oral con preservativo","Oral sin preservativo","Orgias","Parejas","Trio"];

    private $atributos = ["Busto grande","Busto mediano","Busto pequeño","Cara visible","Cola grande","Cola mediana","Cola pequeña","Con video","Contextura delgada","Contextura grande","Contextura mediana","Depilacion full","Depto propio","En promocion","English","Escort independiente","Español","Estatura alta","Estatura mediana","Estatura pequeña","Hentai","Morena","Mulata","No fuma","Ojos claros","Ojos oscuros","Peliroja","Portugues","Relato erotico","Rubia","Tatuajes","Trigueña"];
    
    private $nacionalidades = ["argentina", "brasil", "chile", "colombia", "ecuador", "uruguay"];

    private $categorias = ["premium", "vip", "de_lujo"];

    public function index() 
    {
        $servicios = $this->servicios;
        $atributos = $this->atributos;
        $metaTags = MetaTag::all();

        $usuarioAutenticado = Auth::user();
        
        return view('seo.seofilters', compact('servicios', 'atributos', 'usuarioAutenticado', 'metaTags'));
    }

    public function updateFilter(Request $request, $page, $filter = null) 
    {
        try {
            $fullPath = $filter ? "$page/$filter" : $page;
            
            $validated = $request->validate([
                'meta_title' => 'required',
                'meta_description' => 'required',
                'meta_robots' => 'required'
            ]);
    
            $tipo = $this->determinarTipoFiltro(explode('/', $fullPath));
    
            MetaTag::updateOrCreate(
                ['page' => $fullPath],
                array_merge($validated, ['tipo' => $tipo])
            );
    
            return redirect()->back()->with('success', 'SEO actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    private function determinarTipoFiltro($parts) 
    {
       $path = implode('/', $parts);
    
       if (count($parts) == 1 && $parts[0] == 'seo') {
           return 'sectores'; 
       }
    
       if ($parts[1] == 'escorts-nacionalidad') {
           return 'nacionalidad';
       }
    
       if ($parts[1] == 'escorts-categoria') {
           return 'categorias';
       }

       if ($parts[1] == 'edad') {
        return 'edad';
       }

       if ($parts[1] == 'precio') {
        return 'precio';
    }
       
       if (str_starts_with($path, 'seo/servicios')) {
           return 'servicios';
       }
    
       if (str_starts_with($path, 'seo/atributos')) {
           return 'atributos'; 
       }
       
       $ultimoPart = end($parts);
    
       if (count($parts) == 2 && !str_contains($ultimoPart, 'escorts-')) {
           return 'sector';
       }

       if (in_array($ultimoPart, $this->servicios)) return 'servicio';
       if (in_array($ultimoPart, $this->atributos)) return 'atributo';
    
       foreach ($this->nacionalidades as $nacionalidad) {
           if (str_contains($ultimoPart, "escorts-$nacionalidad")) {
               return 'nacionalidad';
           }
       }
    
       if (str_contains($ultimoPart, 'escorts-con-resenas')) return 'resena';
    
       foreach ($this->categorias as $categoria) {
           if (str_contains($ultimoPart, "escorts-$categoria")) {
               return 'categoria';
           }
       }
    
       throw new \Exception('Tipo de filtro no reconocido');
    }

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