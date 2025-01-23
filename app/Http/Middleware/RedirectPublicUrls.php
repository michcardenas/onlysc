<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RedirectPublicUrls
{
    public function handle(Request $request, Closure $next)
    {
        Log::info([
            'full_url' => $request->fullUrl(),
            'path' => $request->path(),
            'is_public' => $request->is('public/*')
        ]);
        
        if ($request->is('public/*')) {
            return redirect($request->path());
        }
        return $next($request);
    }
}