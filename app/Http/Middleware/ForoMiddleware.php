<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Permitir acceso a rol 1 (admin) y rol 3
        if (Auth::check() && (Auth::user()->rol == 1 || Auth::user()->rol == 3)) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección');
    }
}

