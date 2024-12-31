<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\EscortLocation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ShareBarriosMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Log al inicio del middleware
        Log::info('ShareBarriosMiddleware iniciado');

        try {
            // Log antes de obtener los barrios
            Log::info('Intentando obtener barrios');

            // Cachear los barrios por 24 horas
            $barriosSantiago = Cache::remember('barrios_santiago', 24*60*60, function() {
                Log::info('Cache miss - Obteniendo barrios de la base de datos');
                
                $locations = EscortLocation::select('direccion')
                    ->whereHas('usuarioPublicate', function($query) {
                        $query->whereIn('estadop', [1, 3]);
                    })
                    ->where('ciudad', 'like', '%santiago%')
                    ->get();

                Log::info('Consulta ejecutada', ['count' => $locations->count()]);

                $barrios = $locations->map(function($location) {
                        $direccion = $location->direccion;
                        $partes = array_map('trim', explode(',', $direccion));
                        
                        if (count($partes) >= 2) {
                            $segundaParte = $partes[1];
                            $partesComuna = array_map('trim', explode(' ', $segundaParte));
                            $comuna = end($partesComuna);
                            
                            $comuna = preg_replace('/^(\d+\s+)?/', '', $comuna);
                            $comuna = trim($comuna);
                            
                            return $comuna;
                        }
                        
                        return null;
                    })
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();

                Log::info('Barrios procesados', ['count' => count($barrios)]);
                return $barrios;
            });

            Log::info('Barrios obtenidos correctamente', ['count' => count($barriosSantiago)]);

            // Compartir con todas las vistas
            View::share('barriosSantiago', $barriosSantiago);
            Log::info('Barrios compartidos con View::share');
            
            // También guardar en la sesión
            session(['barriosSantiago' => $barriosSantiago]);
            Log::info('Barrios guardados en sesión');

        } catch (\Exception $e) {
            Log::error('Error en ShareBarriosMiddleware', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        Log::info('ShareBarriosMiddleware finalizado');
        return $next($request);
    }
}