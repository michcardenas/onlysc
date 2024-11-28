<?php

namespace App\Http\Controllers;

use App\Models\UsuarioPublicate;
use App\Models\Ciudad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function show($id)
    {
        // Obtener todas las ciudades
        $ciudades = Ciudad::all();

        // Buscar el usuario en la base de datos por ID
        $usuarioPublicate = UsuarioPublicate::findOrFail($id);

        // Retornar la vista y pasar la información del usuario y las ciudades
        return view('layouts.showescort', compact('usuarioPublicate', 'ciudades'));
    }

    // Mostrar el perfil del administrador
    public function index()
    {
        $usuarioAutenticado = auth()->user();
        $usuario = User::find($usuarioAutenticado->id);

        return view('admin.perfiladmin', compact('usuario', 'usuarioAutenticado'));
    }

    // Actualizar el perfil del administrador
    public function updateProfile(Request $request)
    {
        $usuario = auth()->user();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
            'descripcion' => 'nullable|string'
        ]);

        $usuario->name = $request->nombre;
        $usuario->email = $request->email;
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        $usuario->descripcion = $request->descripcion;
        $usuario->save();

        return redirect()->route('admin.profile')->with('success', 'Perfil actualizado correctamente');
    }

    // Actualizar la foto del perfil
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $usuario = auth()->user();

        if ($request->hasFile('foto')) {
            // Eliminar la foto anterior si existe
            if ($usuario->foto) {
                Storage::disk('public')->delete($usuario->foto);
            }

            // Guardar la nueva foto
            $imagenPath = $request->file('foto')->store('profile_photos', 'public');
            $usuario->foto = $imagenPath;
            $usuario->save();
        }

        return redirect()->route('admin.profile')->with('success', 'Foto de perfil actualizada correctamente');
    }
}
