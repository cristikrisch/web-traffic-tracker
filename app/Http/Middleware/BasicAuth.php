<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = env('ADMIN_USER');
        $pass = env('ADMIN_PASS');

        if (!$user || !$pass) {
            return $next($request); // no auth if not set
        }

        if (
            $request->getUser() === $user &&
            $request->getPassword() === $pass
        ) {
            return $next($request);
        }

        return response('Auth required', 401)->header('WWW-Authenticate', 'Basic realm="Admin"');
    }
}
