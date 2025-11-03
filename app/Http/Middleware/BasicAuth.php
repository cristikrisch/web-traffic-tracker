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

        // No auth if env not set
        if (!$user || !$pass) {
            return $next($request);
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
