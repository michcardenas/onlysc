<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioPublicate;
use App\Models\Disponibilidad;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class UsuarioPublicateController extends Controller
{
    public function edit($id)
    {
        try {
            // Obtener el usuario
            $usuario = UsuarioPublicate::findOrFail($id);
            
            // Obtener la disponibilidad
            $disponibilidad = Disponibilidad::where('publicate_id', $id)->get();
            
            // Preparar los datos para la vista
            $diasDisponibles = $disponibilidad->pluck('dia')->toArray();
            $horarios = [];
            
            foreach($disponibilidad as $disp) {
                $horarios[$disp->dia] = [
                    'desde' => $disp->hora_desde,
                    'hasta' => $disp->hora_hasta
                ];
            }
            
            // Pasar a la vista
            return view('admin.edit', compact('usuario', 'diasDisponibles', 'horarios'));
            
        } catch (\Exception $e) {
            Log::error("Error al cargar el formulario de edición: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info("Iniciando actualización del usuario con ID: $id");

            $usuario = UsuarioPublicate::findOrFail($id);
            Log::info("Usuario encontrado: {$usuario->id}");

            // Validar los datos del formulario
            $request->validate([
                'fantasia' => 'required|string|max:255',
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telefono' => 'nullable|string|max:20',
                'ubicacion' => 'required|string|max:255',
                'edad' => 'required|integer|min:18|max:100',
                'color_ojos' => 'nullable|string|max:50',
                'altura' => 'nullable|numeric|min:0|max:300',
                'peso' => 'nullable|numeric|min:0|max:300',
                'disponibilidad' => 'nullable|string',
                'servicios' => 'nullable|string',
                'servicios_adicionales' => 'nullable|string',
                'atributos' => 'nullable|array',
                'atributos.*' => 'string',
                'nacionalidad' => 'required|string|max:100',
                'cuentanos' => 'nullable|string',
                'estadop' => 'required|integer|in:0,1',
                'categorias' => 'required|string|in:deluxe,premium,VIP,masajes',
                'posicion' => 'nullable|integer|unique:usuarios_publicate,posicion,' . $usuario->id,
                'precio' => 'nullable|numeric|min:0',
                'fotos.*' => 'nullable|image|max:2048',
                'dias_disponibles' => 'array',
                'dias_disponibles.*' => 'string',
                'horario' => 'array',
                'horario.*' => 'array',
            ]);

            Log::info("Validación completada para el usuario con ID: $id");

            // Actualizar los datos del usuario
            $usuario->update([
                'fantasia' => $request->fantasia,
                'nombre' => $request->nombre,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'ubicacion' => $request->ubicacion,
                'edad' => $request->edad,
                'color_ojos' => $request->color_ojos,
                'altura' => $request->altura,
                'peso' => $request->peso,
                'disponibilidad' => $request->disponibilidad,
                'servicios' => $request->servicios,
                'servicios_adicionales' => $request->servicios_adicionales,
                'atributos' => json_encode($request->atributos ?? []),
                'nacionalidad' => $request->nacionalidad,
                'cuentanos' => $request->cuentanos,
                'estadop' => $request->estadop,
                'categorias' => $request->categorias,
                'posicion' => $request->posicion,
                'precio' => $request->precio,
            ]);

            // Resto del código...
            // (Mantener el código de disponibilidad, fotos y creación de usuario igual)

            return redirect()->route('panel_control')->with('success', 'Usuario actualizado correctamente.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Error: El usuario con ID $id no existe.");
            return redirect()->route('panel_control')->with('error', 'El usuario no existe.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Error de validación: " . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Error general al actualizar el usuario: " . $e->getMessage());
            return redirect()->route('panel_control')->with('error', 'Ocurrió un error al actualizar el usuario. Inténtalo de nuevo.');
        }
    }
}