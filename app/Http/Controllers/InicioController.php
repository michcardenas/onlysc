<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\UsuarioPublicate;
use Carbon\Carbon;

class InicioController extends Controller
{
    public function show()
    {
        // Obtener todas las ciudades
        $ciudades = Ciudad::all();

        // Obtener la hora y día actuales
        $now = Carbon::now();
        $currentDay = strtolower($now->locale('es')->dayName);
        $currentTime = $now->format('H:i:s');

        // Obtener todos los usuarios (estadop 1 y 3)
        $usuarios = UsuarioPublicate::with('disponibilidad')
            ->whereIn('estadop', [1, 3])
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'categorias',
                'posicion',
                'precio',
                'estadop'
            )
            ->orderBy('posicion', 'asc')
            ->paginate(12)
            ->appends(request()->query());

        // Mantener la consulta separada para usuario destacado
        $usuarioDestacado = UsuarioPublicate::where('estadop', 3)
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'categorias',
                'precio',
                'estadop'
            )
            ->first();

        // Obtener usuarios online para el panel lateral
        $usuariosOnline = UsuarioPublicate::with(['disponibilidad' => function ($query) use ($currentDay, $currentTime) {
            $query->where('dia', 'LIKE', $currentDay)
                ->where(function ($q) use ($currentTime) {
                    $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                        ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                });
        }])
            ->where('estadop', 1)
            ->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            })
            ->select('id', 'fantasia', 'edad', 'fotos', 'estadop')
            ->take(11)
            ->get();

        return view('inicio', [
            'ciudades' => $ciudades,
            'usuarios' => $usuarios,
            'usuarioDestacado' => $usuarioDestacado,
            'usuariosOnline' => $usuariosOnline,
            'totalOnline' => $usuariosOnline->count(),
            'currentTime' => $currentTime,
            'currentDay' => $currentDay
        ]);
    }

    public function showPerfil($id)
    {
        $usuario = UsuarioPublicate::with('disponibilidad')
            ->findOrFail($id);
        return view('perfil', ['usuario' => $usuario]);
    }
}
