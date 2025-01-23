<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class RedirectPublicUrls
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('public/*')) {
            return redirect(substr($request->path(), 7));
        }
        return $next($request);
    }
}