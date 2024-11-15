<?php

namespace App\Http\Controllers;

use App\Models\Disponibilidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // Obtener el ID del usuario publicado
        $publicateId = auth()->user()->publicate->id;

        try {
            $this->updateDisponibilidad(
                $publicateId,
                $request->dias_disponibles,
                $request->horario,
                $request->fulltime ?? []
            );

            return redirect()->back()->with('success', 'Horario guardado correctamente');
        } catch (\Exception $e) {
            Log::error("Error al guardar disponibilidad: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar el horario');
        }
    }

    public function index()
    {
        // Obtener el ID del usuario publicado
        $publicateId = auth()->user()->publicate->id;

        // Obtener la disponibilidad actual
        $disponibilidad = Disponibilidad::where('publicate_id', $publicateId)
            ->where('estado', 'activo')
            ->get()
            ->keyBy('dia')
            ->toArray();

        return view('tu-vista', compact('disponibilidad'));
    }

    public function updateDisponibilidad($publicateId, $diasDisponibles, $horarios, $fullTime = [])
    {
        try {
            Log::info('Iniciando actualización de disponibilidad', [
                'publicate_id' => $publicateId,
                'dias_disponibles' => $diasDisponibles,
                'horarios' => $horarios,
                'fullTime' => $fullTime
            ]);

            // Eliminar disponibilidad existente
            Disponibilidad::where('publicate_id', $publicateId)->delete();

            foreach ($diasDisponibles as $dia) {
                if (isset($horarios[$dia])) {
                    $horaDesde = isset($fullTime[$dia]) ? '00:00' : $horarios[$dia]['desde'];
                    $horaHasta = isset($fullTime[$dia]) ? '23:59' : $horarios[$dia]['hasta'];

                    Disponibilidad::create([
                        'publicate_id' => $publicateId,
                        'dia' => $dia,
                        'hora_desde' => $horaDesde,
                        'hora_hasta' => $horaHasta,
                        'estado' => 'activo'
                    ]);

                    Log::info("Horario creado para el día $dia", [
                        'desde' => $horaDesde,
                        'hasta' => $horaHasta,
                        'full_time' => isset($fullTime[$dia])
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error en updateDisponibilidad: " . $e->getMessage());
            throw $e;
        }
    }
}