<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RedirectPublicUrls
{
    public function handle(Request $request, Closure $next)
    {
        if (str_contains($request->url(), '/public/')) {
            $newUrl = str_replace('/public/', '/', $request->url());
            \Log::debug('Redirecting from: ' . $request->url() . ' to: ' . $newUrl);
            return redirect($newUrl);
        }
        return $next($request);
    }
}