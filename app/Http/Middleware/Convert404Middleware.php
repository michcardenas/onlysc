<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Convert404Middleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (in_array($response->status(), [200])) {
            abort(404);
        }
        
        return $response;
    }
    
}
