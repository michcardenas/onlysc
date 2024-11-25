<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ciudad;
use App\Models\BlogCategory;
use App\Models\BlogArticle;
use App\Models\BlogTag;
use App\Models\Foro;
use App\Models\Posts;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ForoController extends Controller
{
    public function showForo()
    {
        // Obtenemos todos los foros ordenados por fecha
        $foros = DB::table('foro')
            ->select('foro.*', 'users.name as nombre_usuario')
            ->leftJoin('users', 'foro.id_usuario', '=', 'users.id')
            ->orderBy('posicion', 'asc')
            ->get();
    
        // Solo obtenemos las ciudades que necesita el foro
        $ciudades = Ciudad::all();
    
        return view('foro', [
            'foros' => $foros,
            'ciudades' => $ciudades
        ]);
    }
    
    public function show_foro($categoria)
    {
        $ciudades = Ciudad::all();
    
        $foros = DB::table('foro')
            ->select('foro.*', 'users.name as nombre_usuario')
            ->leftJoin('users', 'foro.id_usuario', '=', 'users.id')
            ->orderBy('fecha', 'desc')
            ->get();
    
        $categorias = $foros->groupBy('id_blog');
    
        if (!isset($categorias[$categoria])) {
            abort(404);
        }
    
        $categoriaActual = (object)[
            'titulo' => $categorias[$categoria]->first()->titulo,
            'descripcion' => $categorias[$categoria]->first()->subtitulo,
            'contenido' => $categorias[$categoria]->first()->contenido,
            'foto' => $categorias[$categoria]->first()->foto,
            'foros' => $categorias[$categoria]->sortBy('posicion')
        ];
    
        return view('layouts.show_foro', [
            'categoria' => $categoriaActual,
            'ciudades' => $ciudades
        ]);
    }

    public function foroadmin()
    {
        $foros = Foro::select('foro.*', 'users.name as nombre_usuario')
            ->leftJoin('users', 'foro.id_usuario', '=', 'users.id')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('admin.foroadmin', [
            'foros' => $foros,
            'usuarioAutenticado' => Auth::user()
        ]);
    }

    public function create()
    {
        $usuarioAutenticado = Auth::user();
        return view('admin.foroadmincreate', compact('usuarioAutenticado'));
    }

    public function store(Request $request)
{
    // Limpiar el contenido HTML
    $contenidoLimpio = strip_tags($request->contenido);
    $request->merge(['contenido' => $contenidoLimpio]);

    // Validación de los campos
    $validated = $request->validate([
        'titulo' => 'required|max:255',
        'subtitulo' => 'required|max:255',
        'contenido' => 'required',
        'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    // Almacenar la foto
    $fotoPath = $request->file('foto')->store('foros', 'public');

    // Obtener la última posición
    $lastPosition = Foro::max('posicion') ?? 0;
    $newPosition = $lastPosition + 1;

    // Crear un nuevo foro
    $foro = Foro::create([
        'titulo' => $validated['titulo'],
        'subtitulo' => $validated['subtitulo'],
        'contenido' => $contenidoLimpio,
        'foto' => $fotoPath,
        'id_usuario' => auth()->id(),
        'fecha' => now(),
        'posicion' => $newPosition
    ]);

    // Actualizar el id_blog para que sea igual al id
    $foro->id_blog = $foro->id;
    $foro->save();

    return redirect()->route('foroadmin')->with('success', 'Foro creado exitosamente');
}

    public function edit($id)
    {
        // Obtener el foro a editar
        $foro = Foro::findOrFail($id);
        $usuarioAutenticado = Auth::user();
        return view('admin.foroadminedit', compact('foro', 'usuarioAutenticado'));
    }

    public function update(Request $request, $id)
    {
        // Obtener el foro a actualizar
        $foro = Foro::findOrFail($id);

        // Limpiar el contenido HTML
        $contenidoLimpio = strip_tags($request->contenido);
        $request->merge(['contenido' => $contenidoLimpio]);

        // Validación de los campos
        $validated = $request->validate([
            'titulo' => 'required|max:255',
            'subtitulo' => 'required|max:255',
            'contenido' => 'required',
            'foto' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable'
        ]);

        // Si se subió una nueva foto
        if ($request->hasFile('foto')) {
            // Eliminar la foto anterior si existe
            if ($foro->foto) {
                Storage::disk('public')->delete($foro->foto);
            }
            $fotoPath = $request->file('foto')->store('foros', 'public');
            $foro->foto = $fotoPath;
        }

        // Actualizar otros campos usando el contenido limpio
        $foro->titulo = $validated['titulo'];
        $foro->subtitulo = $validated['subtitulo'];
        $foro->contenido = $contenidoLimpio;
        $foro->id_blog = $request->input('id_blog');
        $foro->save();

        return redirect()->route('foroadmin')->with('success', 'Foro actualizado exitosamente');
    }

    public function destroy($id)
    {
        // Eliminar el foro
        $foro = Foro::findOrFail($id);
        if ($foro->foto) {
            Storage::disk('public')->delete($foro->foto);
        }
        $foro->delete();

        return redirect()->route('foroadmin')->with('success', 'Foro eliminado exitosamente');
    }

    public function showPosts($id_blog = null)
    {
        $query = Posts::with(['usuario', 'foro'])
                      ->orderBy('is_fixed', 'desc')  // Primero ordenar por fijados
                      ->orderBy('created_at', 'desc'); // Luego por fecha
        
        if ($id_blog) {
            $query->where('id_blog', $id_blog);
        }
        
        $posts = $query->get();
        $usuarioAutenticado = Auth::user();
        
        return view('admin.posts', [
            'posts' => $posts,
            'id_blog' => $id_blog,
            'usuarioAutenticado' => $usuarioAutenticado
        ]);
    }
    
    public function editpost($id)
    {
        try {
            $post = Posts::findOrFail($id);
            
            if (request()->ajax()) {
                return response()->json([
                    'id' => $post->id,
                    'titulo' => $post->titulo,
                    'id_blog' => $post->id_blog,
                    'is_fixed' => $post->is_fixed // Agregar el estado is_fixed
                ]);
            }
            
            return redirect()->back()->with('error', 'Método no permitido');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Post no encontrado'], 404);
            }
            return redirect()->back()->with('error', 'Post no encontrado');
        }
    }
    
    public function storepost(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|max:255',
                'id_blog' => 'required|exists:foro,id',
                'is_fixed' => 'nullable|boolean' // Agregar validación para is_fixed
            ]);
    
            $post = Posts::create([
                'titulo' => strip_tags($request->titulo),
                'id_blog' => $request->id_blog,
                'id_usuario' => Auth::id(),
                'is_fixed' => $request->has('is_fixed') // Procesar el checkbox
            ]);
    
            if ($request->ajax()) {
                return response()->json(['redirect' => route('foroadmin', ['id_blog' => $request->id_blog])]);
            }
            
            return redirect()->route('foroadmin', ['id_blog' => $request->id_blog])
                           ->with('success', 'Post creado exitosamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function updatepost(Request $request, $id)
    {
        try {
            $post = Posts::findOrFail($id);
    
            $validated = $request->validate([
                'titulo' => 'required|max:255',
                'is_fixed' => 'nullable|boolean' // Agregar validación para is_fixed
            ]);
    
            $post->titulo = strip_tags($request->titulo);
            $post->is_fixed = $request->has('is_fixed'); // Procesar el checkbox
            $post->save();
    
            if ($request->ajax()) {
                return response()->json(['redirect' => route('foroadmin', ['id_blog' => $post->id_blog])]);
            }
            
            return redirect()->route('foroadmin', ['id_blog' => $post->id_blog])
                           ->with('success', 'Post actualizado exitosamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function destroypost($id)
    {
        try {
            $post = Posts::findOrFail($id);
            $id_blog = $post->id_blog;
            $post->delete();
    
            if (request()->ajax()) {
                return response()->json(['redirect' => route('foroadmin', ['id_blog' => $id_blog])]);
            }
            
            return redirect()->route('foroadmin', ['id_blog' => $id_blog])
                           ->with('success', 'Post eliminado exitosamente');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Nuevo método para toggle de fijado
    public function toggleFixed($id)
    {
        try {
            $post = Posts::findOrFail($id);
            $post->is_fixed = !$post->is_fixed;
            $post->save();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'is_fixed' => $post->is_fixed,
                    'message' => $post->is_fixed ? 'Post fijado exitosamente' : 'Post desfijado exitosamente'
                ]);
            }

            return redirect()->back()->with('success', 
                $post->is_fixed ? 'Post fijado exitosamente' : 'Post desfijado exitosamente');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
