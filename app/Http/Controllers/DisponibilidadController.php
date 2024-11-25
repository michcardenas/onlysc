<?php

namespace App\Http\Controllers;

use App\Models\Disponibilidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DisponibilidadController extends Controller
{
    public function store(Request $request)
    {
        // Validación de datos modificada para aceptar "Full Time"
        $request->validate([
            'dias_disponibles' => 'required|array',
            'dias_disponibles.*' => 'required|string',
            'horario' => 'required|array',
            'horario.*' => 'required|array',
            'horario.*.desde' => 'required|string',
            'horario.*.hasta' => 'required|string',
        ]);

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
        $publicateId = auth()->user()->publicate->id;

        // Modificado para incluir información de Full Time
        $disponibilidades = Disponibilidad::where('publicate_id', $publicateId)
            ->where('estado', 'activo')
            ->get();

        $disponibilidad = $disponibilidades->map(function ($item) {
            $isFullTime = $item->hora_desde === '00:00' && $item->hora_hasta === '23:59';
            return [
                'dia' => $item->dia,
                'desde' => $isFullTime ? 'Full Time' : $item->hora_desde,
                'hasta' => $isFullTime ? 'Full Time' : $item->hora_hasta,
                'is_full_time' => $isFullTime
            ];
        })->keyBy('dia')->toArray();

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
                    $isFullTime = isset($fullTime[$dia]) || 
                        (isset($horarios[$dia]['desde']) && $horarios[$dia]['desde'] === 'Full Time') ||
                        (isset($horarios[$dia]['hasta']) && $horarios[$dia]['hasta'] === 'Full Time');

                    if ($isFullTime) {
                        $horaDesde = '00:00';
                        $horaHasta = '23:59';
                    } else {
                        $horaDesde = $horarios[$dia]['desde'];
                        $horaHasta = $horarios[$dia]['hasta'];
                    }

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
                        'full_time' => $isFullTime
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