<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad; // O el modelo que estés usando
use App\Models\Foro;
use Illuminate\Support\Facades\Storage;


class ForoController extends Controller
{
    public function showForo()
    {
        // Lógica de consulta a la base de datos
        $ciudades = Ciudad::all();  // Ejemplo: obtienes todas las ciudades

        // Retorna la vista con los datos consultados
        return view('foro', ['ciudades' => $ciudades]);
    }

    public function show_foro($categoria)
    {
        // Agregar la consulta de ciudades aquí también
        $ciudades = Ciudad::all();

        $categorias = [
            'conversaciones' => [
                'titulo' => 'Conversaciones sobre Sexo',
                'descripcion' => 'Bienvenidos a "Charla sobre Sexo", un espacio sin tabúes para discutir todo lo relacionado con la sexualidad.',
                'imagen' => 'foro1.jpg'
            ],
            'experiencias' => [
                'titulo' => 'Experiencias',
                'descripcion' => 'Descubre y comparte tu experiencia con las chicas de la plataforma.',
                'imagen' => 'foro2.jpeg'
            ],
            'gentlemens-club' => [
                'titulo' => "Gentlemen's Club",
                'descripcion' => 'Para hablar con libertad de lo que desees.',
                'imagen' => 'pexels-79380313-9007274-scaled.jpg'
            ]
        ];

        if (!isset($categorias[$categoria])) {
            abort(404);
        }

        return view('layouts.show_foro', [
            'categoria' => (object)$categorias[$categoria],
            'ciudades' => $ciudades
        ]);
    }

    public function foroadmin()
    {
        $forosConversaciones = Foro::where('id_blog', 1)->orderBy('fecha', 'desc')->get();
        $forosGentlemen = Foro::where('id_blog', 2)->orderBy('fecha', 'desc')->get();
        $forosExperiencias = Foro::where('id_blog', 3)->orderBy('fecha', 'desc')->get();

        $usuarioAutenticado = auth()->user();

        return view('admin.foroadmin', compact('forosConversaciones', 'forosGentlemen', 'forosExperiencias', 'usuarioAutenticado'));
    }

    public function create()
    {
        $usuarioAutenticado = auth()->user();
        return view('admin.foroadmincreate', compact('usuarioAutenticado'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|max:255',
            'subtitulo' => 'required|max:255',
            'contenido' => 'required',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_blog' => 'required|exists:blogs,id', // Asegúrate de que id_blog sea válido
        ]);

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foros', 'public');
        }

        Foro::create([
            'titulo' => $validated['titulo'],
            'subtitulo' => $validated['subtitulo'],
            'contenido' => $validated['contenido'],
            'foto' => $fotoPath ?? null,
            'id_usuario' => auth()->id(),
            'fecha' => now(),
            'id_blog' => $validated['id_blog'], // Guardar id_blog
        ]);

        return redirect()->route('foroadmin')->with('success', 'Foro creado exitosamente');
    }


    public function edit($id)
    {
        $foro = Foro::findOrFail($id);
        $usuarioAutenticado = auth()->user();
        return view('admin.foroadminedit', compact('foro', 'usuarioAutenticado'));
    }


    public function update(Request $request, $id)
    {
        $foro = Foro::findOrFail($id);

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
        $foro = Foro::findOrFail($id);
        if ($foro->foto) {
            Storage::disk('public')->delete($foro->foto);
        }
        $foro->delete();

        return redirect()->route('foroadmin')->with('success', 'Foro eliminado exitosamente');
    }
}
