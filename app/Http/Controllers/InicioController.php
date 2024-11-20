<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\UsuarioPublicate;
use Carbon\Carbon;

class InicioController extends Controller
{
    public function show($nombreCiudad)
    {
        // Buscar la ciudad por nombre
        $ciudadSeleccionada = Ciudad::where('nombre', $nombreCiudad)->first();
    
        if (!$ciudadSeleccionada) {
            // Si la ciudad no existe, mostrar un error 404
            abort(404, 'Ciudad no encontrada');
        }
    
        // Obtener todas las ciudades
        $ciudades = Ciudad::all();
    
        // Obtener la hora y día actuales
        $now = Carbon::now();
        $currentDay = strtolower($now->locale('es')->dayName);
        $currentTime = $now->format('H:i:s');
    
        // Filtrar usuarios por la ciudad seleccionada
        $usuarios = UsuarioPublicate::with('disponibilidad')
            ->whereIn('estadop', [1, 3])
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'posicion',
                'precio',
                'estadop'
            )
            ->orderBy('posicion', 'asc')
            ->paginate(12)
            ->appends(request()->query());
    
        // Obtener el usuario destacado
        $usuarioDestacado = UsuarioPublicate::where('estadop', 3)
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'precio',
                'estadop'
            )
            ->first();
    
        // Usuarios online
        $usuariosOnline = UsuarioPublicate::with(['disponibilidad' => function ($query) use ($currentDay, $currentTime) {
            $query->where('dia', 'LIKE', $currentDay)
                ->where(function ($q) use ($currentTime) {
                    $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                        ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                });
        }])
            ->where('estadop', 1)
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            })
            ->select('id', 'fantasia', 'edad', 'fotos', 'foto_positions', 'estadop')
            ->take(11)
            ->get();
    
        return view('inicio', [
            'ciudades' => $ciudades,
            'ciudadSeleccionada' => $ciudadSeleccionada,
            'usuarios' => $usuarios,
            'usuarioDestacado' => $usuarioDestacado,
            'usuariosOnline' => $usuariosOnline,
            'totalOnline' => $usuariosOnline->count(),
            'currentTime' => $currentTime,
            'currentDay' => $currentDay
        ]);
    }
    
    public function showByCategory($nombreCiudad, $categoria)
{
      // Convertir la categoría a mayúsculas
      $categoria = strtoupper($categoria);
    // Buscar la ciudad por nombre
    $ciudadSeleccionada = Ciudad::where('nombre', $nombreCiudad)->first();

    if (!$ciudadSeleccionada) {
        abort(404, 'Ciudad no encontrada');
    }

    // Validar la categoría
    $categoriasPermitidas = ['DELUXE', 'VIP', 'PREMIUM', 'MASAJES'];
    if (!in_array($categoria, $categoriasPermitidas)) {
        abort(404, 'Categoría no válida');
    }

    // Obtener todas las ciudades
    $ciudades = Ciudad::all();

    // Obtener la hora y día actuales
    $now = Carbon::now();
    $currentDay = strtolower($now->locale('es')->dayName);
    $currentTime = $now->format('H:i:s');

    // Filtrar usuarios por ciudad y categoría
    $usuarios = UsuarioPublicate::with('disponibilidad')
        ->whereIn('estadop', [1, 3])
        ->where('ubicacion', $ciudadSeleccionada->nombre)
        ->where('categorias', $categoria) // Filtrar por categoría
        ->select(
            'id',
            'fantasia',
            'nombre',
            'edad',
            'ubicacion',
            'fotos',
            'foto_positions',
            'categorias',
            'posicion',
            'precio',
            'estadop'
        )
        ->orderBy('posicion', 'asc')
        ->paginate(12)
        ->appends(request()->query());

    // Usuario destacado
    $usuarioDestacado = UsuarioPublicate::where('estadop', 3)
        ->where('ubicacion', $ciudadSeleccionada->nombre)
        ->where('categorias', $categoria) // Filtrar por categoría
        ->select(
            'id',
            'fantasia',
            'nombre',
            'edad',
            'ubicacion',
            'fotos',
            'foto_positions',
            'categorias',
            'precio',
            'estadop'
        )
        ->first();

    // Usuarios online
    $usuariosOnline = UsuarioPublicate::with(['disponibilidad' => function ($query) use ($currentDay, $currentTime) {
        $query->where('dia', 'LIKE', $currentDay)
            ->where(function ($q) use ($currentTime) {
                $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                    ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
            });
    }])
        ->where('estadop', 1)
        ->where('ubicacion', $ciudadSeleccionada->nombre)
        ->where('categorias', $categoria) // Filtrar por categoría
        ->select('id', 'fantasia', 'edad', 'fotos', 'foto_positions', 'estadop')
        ->take(11)
        ->get();

    return view('inicio', [
        'ciudades' => $ciudades,
        'ciudadSeleccionada' => $ciudadSeleccionada,
        'usuarios' => $usuarios,
        'usuarioDestacado' => $usuarioDestacado,
        'usuariosOnline' => $usuariosOnline,
        'totalOnline' => $usuariosOnline->count(),
        'currentTime' => $currentTime,
        'currentDay' => $currentDay,
        'categoriaSeleccionada' => $categoria // Pasar la categoría seleccionada a la vista
    ]);
}


    public function showPerfil($id)
    {
        $usuario = UsuarioPublicate::with('disponibilidad')
            ->findOrFail($id);
        return view('perfil', ['usuario' => $usuario]);
    }
}