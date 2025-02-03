<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\Servicio;
use App\Models\Atributo;
use App\Models\Sector;
use App\Models\Nacionalidad;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function contacto()
    {
        // Obtener todas las ciudades
        $ciudades = Ciudad::all();

        // Agrupar las ciudades por zona (para el menú de Ciudades)
        $ciudadesPorZona = $ciudades->groupBy('zona');

        // Obtener servicios y atributos ordenados por 'posicion'
        $servicios = Servicio::orderBy('posicion')->get();
        $atributos = Atributo::orderBy('posicion')->get();

        // Obtener sectores y nacionalidades
        $sectores = Sector::all();
        $nacionalidades = Nacionalidad::all();

        // Retornamos la vista 'contacto' con los datos necesarios
        return view('contacto', compact(
            'ciudades',         // Para los <select> y el dropdown
            'ciudadesPorZona',  // Para agrupar en el menú 'CIUDADES'
            'servicios',        // Para renderizar servicios en el modal
            'atributos',        // Para renderizar atributos en el modal
            'sectores',         // Para el <select> de sector
            'nacionalidades'    // Para el <select> de nacionalidad
        ));
    }
    public function send(Request $request)
    {
        // 1. Validar datos del formulario
        $validatedData = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);
    
        // 2. Construir el cuerpo HTML del correo
        $htmlBody = "
            <p><strong>Nombre:</strong> {$validatedData['name']}</p>
            <p><strong>Email:</strong> {$validatedData['email']}</p>
            <p><strong>Mensaje:</strong><br>
                {$validatedData['message']}
            </p>
        ";
    
        try {
            // 3. Enviar el correo
            Mail::send([], [], function ($message) use ($validatedData, $htmlBody) {
                // (Opcional) Remitente sea el del usuario:
                // $message->from($validatedData['email'], $validatedData['name']);
    
                // Dirección de destino desde .env o, si no existe, por defecto 'info@onlyescorts.cl'
                $toAddress = env('MAIL_TO_ADDRESS', 'contacto@onlyescorts.cl');
    
                // Registrar en logs que estamos intentando el envío
                \Log::info("Intentando enviar correo a: {$toAddress} - Asunto: {$validatedData['subject']} - Remitente: {$validatedData['email']}");
    
                $message->to($toAddress)
                        ->subject($validatedData['subject'])
                        ->html($htmlBody);
    
                // Registrar en logs que terminamos la configuración del mensaje
                \Log::info("Mensaje configurado correctamente para enviar a {$toAddress}");
            });
    
            // Registrar en logs que la función Mail::send no arrojó excepciones
            \Log::info("Se llamó Mail::send() sin excepciones.");
    
            // 4. Redirigir con un mensaje de éxito
            return redirect()
                ->back()
                ->with('success', 'Tu mensaje se envió correctamente. ¡Gracias por contactarnos!');
        } catch (\Exception $e) {
            // Registrar en logs el error
            \Log::error("Error al enviar correo: " . $e->getMessage());
    
            // Retornar con un mensaje de fallo
            return redirect()
                ->back()
                ->with('error', 'Ocurrió un problema al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.');
        }
    }
    
}
