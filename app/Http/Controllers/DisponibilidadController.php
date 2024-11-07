<?php

namespace App\Http\Controllers;

use App\Models\Disponibilidad;
use Illuminate\Http\Request;

class DisponibilidadController extends Controller
{
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'dias_disponibles' => 'required|array',
            'dias_disponibles.*' => 'required|string',
            'horario' => 'required|array',
            'horario.*' => 'required|array',
            'horario.*.desde' => 'required|string',
            'horario.*.hasta' => 'required|string',
        ]);

        // Obtener el ID del usuario publicado (ajusta esto según tu lógica de autenticación)
        $publicateId = auth()->user()->publicate->id; // O como obtengas el ID

        // Primero eliminamos los registros anteriores de este usuario
        Disponibilidad::where('publicate_id', $publicateId)->delete();

        // Recorremos solo los días seleccionados
        foreach ($request->dias_disponibles as $dia) {
            // Solo creamos registros para los días que están en el array de días seleccionados
            if (isset($request->horario[$dia])) {
                Disponibilidad::create([
                    'publicate_id' => $publicateId,
                    'dia' => $dia,
                    'hora_desde' => $request->horario[$dia]['desde'],
                    'hora_hasta' => $request->horario[$dia]['hasta'],
                    'estado' => 'activo'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Horario guardado correctamente');
    }

    public function index()
    {
        // Obtener el ID del usuario publicado
        $publicateId = auth()->user()->publicate->id; // Ajusta según tu lógica

        // Obtener la disponibilidad actual
        $disponibilidad = Disponibilidad::where('publicate_id', $publicateId)
            ->get()
            ->keyBy('dia')
            ->toArray();

        return view('tu-vista', compact('disponibilidad'));
    }
}