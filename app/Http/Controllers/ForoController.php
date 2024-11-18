<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ciudad; // O el modelo que estés usando
use App\Models\Foro;
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

        // Obtenemos todas las ciudades
        $ciudades = Ciudad::all();

        return view('foro', [
            'foros' => $foros,
            'ciudades' => $ciudades
        ]);
    }

    public function show_foro($categoria)
    {
        // Agregar la consulta de ciudades aquí también
        $ciudades = Ciudad::all();

        // Obtener todos los foros ordenados por fecha
        $foros = DB::table('foro')
            ->select('foro.*', 'users.name as nombre_usuario')
            ->leftJoin('users', 'foro.id_usuario', '=', 'users.id')
            ->orderBy('fecha', 'desc')
            ->get();

        // Agrupar los foros por id_blog
        $categorias = $foros->groupBy('id_blog');

        // Verificar si la categoría solicitada existe
        if (!isset($categorias[$categoria])) {
            abort(404);
        }

        // Obtener la categoría actual
        $categoriaActual = (object)[
            'titulo' => $categorias[$categoria]->first()->titulo,
            'descripcion' => $categorias[$categoria]->first()->subtitulo,
            'contenido' => $categorias[$categoria]->first()->contenido, // Añadido el campo contenido
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
        // Validación de los campos
        $validated = $request->validate([
            'titulo' => 'required|max:255',
            'subtitulo' => 'required|max:255',
            'contenido' => 'required',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_blog' => 'required|exists:foro,id'
        ]);

        // Almacenar la foto
        $fotoPath = $request->file('foto')->store('foros', 'public');

        // Crear un nuevo foro
        Foro::create([
            'titulo' => $validated['titulo'],
            'subtitulo' => $validated['subtitulo'],
            'contenido' => $validated['contenido'],
            'foto' => $fotoPath,
            'id_usuario' => auth()->id(),
            'fecha' => now(),
            'id_blog' => $validated['id_blog'], // Guardar id_blog
        ]);

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
            $foro->foto = $fotoPath; // Guardar la nueva ruta
        }

        // Actualizar otros campos
        $foro->titulo = $validated['titulo'];
        $foro->subtitulo = $validated['subtitulo'];
        $foro->contenido = $validated['contenido'];
        $foro->id_blog = $request->input('id_blog'); // Actualiza el id_blog
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
}
