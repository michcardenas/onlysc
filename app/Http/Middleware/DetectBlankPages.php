<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class DetectBlankPages
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if ($response->getContent() == '') {
            Log::error('Página en blanco detectada', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'params' => $request->all(),
                'user_agent' => $request->userAgent(),
                'session' => $request->session()->all()
            ]);
        }
        
        return $response;
    }
}