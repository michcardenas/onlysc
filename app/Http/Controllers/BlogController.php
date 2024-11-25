<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BlogArticle;
use App\Models\Ciudad;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function showBlog()
    {
        $articulos = BlogArticle::with('user')
            ->where('estado', 'publicado')
            ->orderBy('fecha_publicacion', 'desc')
            ->get();

        return view('blog', [
            'articulos' => $articulos
        ]);
    }

    public function show_article($id)
    {
        $articulo = BlogArticle::findOrFail($id);
        $articulo->increment('visitas');

        // Obtén las ciudades
        $ciudades = Ciudad::all(); // Asegúrate de importar el modelo

        return view('layouts.showblog', [
            'articulo' => $articulo,
            'usuarioAutenticado' => Auth::user(),
            'ciudades' => $ciudades
        ]);
    }

    public function blogadmin()
    {
        $articulos = BlogArticle::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.blogadmin', [
            'articulos' => $articulos,
            'usuarioAutenticado' => Auth::user()
        ]);
    }

    public function create()
    {
        return view('admin.blogadmincreate', [
            'usuarioAutenticado' => Auth::user()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|max:255',
                'contenido' => 'required',
                'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Lista de etiquetas HTML permitidas
            $allowedTags = '<h1><h2><p><br><strong><em><ul><ol><li><a><span><div><img><table><tr><td><th><tbody><thead>';

            // Filtrar el contenido manteniendo las etiquetas permitidas
            $contenidoFiltrado = strip_tags($request->contenido, $allowedTags);

            // Almacenar imagen
            $imagenPath = $request->file('imagen')->store('blog', 'public');

            // Crear artículo
            $articulo = BlogArticle::create([
                'titulo' => $validated['titulo'],
                'slug' => Str::slug($validated['titulo']),
                'contenido' => $contenidoFiltrado,
                'imagen' => $imagenPath,
                'user_id' => Auth::id(),
                'estado' => $request->estado ?? 'borrador',
                'destacado' => $request->has('destacado'),
                'fecha_publicacion' => $request->estado === 'publicado' ? now() : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Artículo creado exitosamente',
                'redirect' => route('blogadmin')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el artículo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $articulo = BlogArticle::findOrFail($id);

            // Asegúrate de que la respuesta sea JSON
            return response()->json([
                'id' => $articulo->id,
                'titulo' => $articulo->titulo,
                'contenido' => $articulo->contenido,
                'estado' => $articulo->estado,
                'destacado' => $articulo->destacado
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar el artículo'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $articulo = BlogArticle::findOrFail($id);

            $validated = $request->validate([
                'titulo' => 'required|max:255',
                'contenido' => 'required',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Lista de etiquetas HTML permitidas
            $allowedTags = '<h1><h2><p><br><strong><em><ul><ol><li><a><span><div><img><table><tr><td><th><tbody><thead>';

            // Filtrar el contenido manteniendo las etiquetas permitidas
            $contenidoFiltrado = strip_tags($request->contenido, $allowedTags);

            // Actualizar imagen si se proporcionó una nueva
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($articulo->imagen) {
                    Storage::disk('public')->delete($articulo->imagen);
                }
                $imagenPath = $request->file('imagen')->store('blog', 'public');
                $articulo->imagen = $imagenPath;
            }

            $articulo->titulo = $validated['titulo'];
            $articulo->slug = Str::slug($validated['titulo']);
            $articulo->contenido = $contenidoFiltrado;
            $articulo->estado = $request->estado ?? 'borrador';
            $articulo->destacado = $request->has('destacado');

            if ($request->estado === 'publicado' && !$articulo->fecha_publicacion) {
                $articulo->fecha_publicacion = now();
            }

            $articulo->save();

            return response()->json([
                'success' => true,
                'message' => 'Artículo actualizado exitosamente',
                'redirect' => route('blogadmin')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el artículo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $articulo = BlogArticle::findOrFail($id);

        if ($articulo->imagen) {
            Storage::disk('public')->delete($articulo->imagen);
        }

        $articulo->delete();

        return redirect()->route('blogadmin')
            ->with('success', 'Artículo eliminado exitosamente');
    }

    public function toggleFeatured($id)
    {
        try {
            $articulo = BlogArticle::findOrFail($id);
            $articulo->destacado = !$articulo->destacado;
            $articulo->save();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'is_featured' => $articulo->destacado,
                    'message' => $articulo->destacado ? 'Artículo destacado exitosamente' : 'Artículo quitado de destacados'
                ]);
            }

            return redirect()->back()->with(
                'success',
                $articulo->destacado ? 'Artículo destacado exitosamente' : 'Artículo quitado de destacados'
            );
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
