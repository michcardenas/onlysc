<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Ciudad;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function showBlog()
    {
        $articulos = BlogArticle::with(['user', 'categories', 'tags'])
            ->where('estado', 'publicado')
            ->orderBy('fecha_publicacion', 'desc')
            ->get();

        $ciudades = Ciudad::all();
        $categorias = BlogCategory::all();
        $tags = BlogTag::all();

        return view('blog', [
            'articulos' => $articulos,
            'ciudades' => $ciudades,
            'categorias' => $categorias,
            'tags' => $tags
        ]);
    }

    public function show_article($id)
    {
        $articulo = BlogArticle::with(['categories', 'tags'])->findOrFail($id);
        $articulo->increment('visitas');

        $articulos = BlogArticle::with(['user', 'categories', 'tags'])
            ->where('estado', 'publicado')
            ->orderBy('fecha_publicacion', 'desc')
            ->get();

        $ciudades = Ciudad::all();
        $categorias = BlogCategory::all();
        $tags = BlogTag::all();

        return view('layouts.showblog', [
            'articulo' => $articulo,
            'articulos' => $articulos,
            'ciudades' => $ciudades,
            'categorias' => $categorias,
            'tags' => $tags,
            'usuarioAutenticado' => Auth::user()
        ]);
    }

    public function blogadmin()
    {
        $articulos = BlogArticle::with(['user', 'categories', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        $categorias = BlogCategory::all();
        $tags = BlogTag::all();

        return view('admin.blogadmin', [
            'articulos' => $articulos,
            'categorias' => $categorias,
            'tags' => $tags,
            'usuarioAutenticado' => Auth::user()
        ]);
    }

    // Nuevo método edit para cargar datos del artículo
    public function edit($id)
    {
        try {
            $articulo = BlogArticle::with(['categories', 'tags'])
                ->findOrFail($id);

            return response()->json([
                'id' => $articulo->id,
                'titulo' => $articulo->titulo,
                'contenido' => $articulo->contenido,
                'estado' => $articulo->estado,
                'destacado' => (bool) $articulo->destacado,
                'imagen' => $articulo->imagen ? Storage::url($articulo->imagen) : null,
                'categories' => $articulo->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'nombre' => $category->nombre
                    ];
                }),
                'tags' => $articulo->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'nombre' => $tag->nombre
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en blog edit: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar el artículo',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|max:255',
                'contenido' => 'required',
                'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'categorias' => 'array',
                'tags' => 'array'
            ]);

            $allowedTags = '<h1><h2><p><br><strong><em><ul><ol><li><a><span><div><img><table><tr><td><th><tbody><thead>';
            $contenidoFiltrado = strip_tags($request->contenido, $allowedTags);
            $imagenPath = $request->file('imagen')->store('blog', 'public');

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

            // Sincronizar categorías y tags
            if ($request->has('categorias')) {
                $articulo->categories()->sync($request->categorias);
            }
            if ($request->has('tags')) {
                $articulo->tags()->sync($request->tags);
            }

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

    public function update(Request $request, $id)
    {
        try {
            $articulo = BlogArticle::findOrFail($id);

            $validated = $request->validate([
                'titulo' => 'required|max:255',
                'contenido' => 'required',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'categorias' => 'array',
                'tags' => 'array'
            ]);

            $allowedTags = '<h1><h2><p><br><strong><em><ul><ol><li><a><span><div><img><table><tr><td><th><tbody><thead>';
            $contenidoFiltrado = strip_tags($request->contenido, $allowedTags);

            if ($request->hasFile('imagen')) {
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

            // Sincronizar categorías y tags
            if ($request->has('categorias')) {
                $articulo->categories()->sync($request->categorias);
            }
            if ($request->has('tags')) {
                $articulo->tags()->sync($request->tags);
            }

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

    // Métodos para Categorías
    public function storeCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|max:255',
                'descripcion' => 'nullable'
            ]);

            BlogCategory::create([
                'nombre' => $validated['nombre'],
                'slug' => Str::slug($validated['nombre']),
                'descripcion' => $validated['descripcion']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCategory(Request $request, $id)
    {
        try {
            $categoria = BlogCategory::findOrFail($id);
            $validated = $request->validate([
                'nombre' => 'required|max:255',
                'descripcion' => 'nullable'
            ]);

            $categoria->update([
                'nombre' => $validated['nombre'],
                'slug' => Str::slug($validated['nombre']),
                'descripcion' => $validated['descripcion']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyCategory($id)
    {
        try {
            $categoria = BlogCategory::findOrFail($id);
            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    // Métodos para Tags
    public function storeTag(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|max:255'
            ]);

            BlogTag::create([
                'nombre' => $validated['nombre'],
                'slug' => Str::slug($validated['nombre'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tag creado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tag: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateTag(Request $request, $id)
    {
        try {
            $tag = BlogTag::findOrFail($id);
            $validated = $request->validate([
                'nombre' => 'required|max:255'
            ]);

            $tag->update([
                'nombre' => $validated['nombre'],
                'slug' => Str::slug($validated['nombre'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tag actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tag: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyTag($id)
    {
        try {
            $tag = BlogTag::findOrFail($id);
            $tag->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tag eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tag: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleDestacado($id)
    {
        try {
            $article = BlogArticle::findOrFail($id);
            $article->destacado = !$article->destacado;
            $article->save();

            return response()->json([
                'success' => true,
                'destacado' => $article->destacado,
                'message' => $article->destacado ?
                    'Artículo marcado como destacado' :
                    'Artículo desmarcado como destacado'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cambiar estado destacado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado destacado'
            ], 500);
        }
    }

    public function editCategory($id)
    {
        try {
            $categoria = BlogCategory::findOrFail($id);
            return response()->json([
                'nombre' => $categoria->nombre,
                'descripcion' => $categoria->descripcion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editTag($id)
    {
        try {
            $tag = BlogTag::findOrFail($id);
            return response()->json([
                'nombre' => $tag->nombre
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el tag: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showCategory($id)
    {
        // Obtener la categoría específica con sus artículos relacionados
        $categoria = BlogCategory::findOrFail($id);
        
        // Obtener los artículos de esta categoría
        $articulos = BlogArticle::with(['user', 'categories', 'tags'])
            ->whereHas('categories', function($query) use ($id) {
                $query->where('blog_categories.id', $id);
            })
            ->where('estado', 'publicado')
            ->orderBy('fecha_publicacion', 'desc')
            ->get();
    
        // Obtener todas las categorías con el conteo de artículos
        $categorias = BlogCategory::withCount(['articles' => function($query) {
            $query->where('estado', 'publicado');
        }])->get();
    
        $ciudades = Ciudad::all();
        $tags = BlogTag::all();
    
        return view('layouts.showcategory', [
            'articulos' => $articulos,
            'ciudades' => $ciudades,
            'categorias' => $categorias,
            'tags' => $tags,
            'categoria' => $categoria
        ]);
    }
}
