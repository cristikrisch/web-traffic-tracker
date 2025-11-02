<?php

namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;

class SecurityHeaders {

    public function handle(Request $req, Closure $next) {
        $res = $next($req);
        $res->headers->set('X-Content-Type-Options','nosniff');
        $res->headers->set('X-Frame-Options','DENY');
        $res->headers->set('Referrer-Policy','strict-origin-when-cross-origin');
        $res->headers->set('Permissions-Policy','geolocation=(), camera=(), microphone=()');

        return $res;
    }
}
