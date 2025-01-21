<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Agrega logs para debugging
        \Log::info('Usuario autenticado: ', ['autenticado' => auth()->check()]);
        
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Debes iniciar sesión.');
        }
        
        \Log::info('Rol del usuario: ', ['rol' => auth()->user()->rol]);
        
        if (auth()->user()->rol != 1) {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        return $next($request);
    }
}