<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowIframeCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Permitir cookies en iframes
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Origin', 'http://127.0.0.1:5173');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-XSRF-TOKEN');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->remove('X-Frame-Options');

        return $response;
    }
}
