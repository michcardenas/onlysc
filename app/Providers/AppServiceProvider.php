<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\EscortLocation;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

          // Compartir los barrios con todas las vistas
          View::composer('*', function ($view) {
            $barriosSantiago = Cache::remember('barrios_santiago', 24*60*60, function() {
                return EscortLocation::select('direccion')
                    ->whereHas('usuarioPublicate', function($query) {
                        $query->whereIn('estadop', [1, 3]);
                    })
                    ->where('ciudad', 'like', '%santiago%')
                    ->get()
                    ->map(function($location) {
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
            });

            $view->with('barriosSantiago', $barriosSantiago);
        });

        date_default_timezone_set('America/Santiago'); 
    }
}
