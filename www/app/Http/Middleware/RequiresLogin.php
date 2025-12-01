<?php

namespace App\Http\Middleware;

use App\Models\Session;
use Closure;
use ApiError;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie(AUTH_TOKEN);

        if ($token === null) {
            return \err(Response::HTTP_UNAUTHORIZED, ApiError::UNAUTHORIZED);
        }

        $decoded = base64_decode($token, true);

        if (!$decoded) {
            return \err(Response::HTTP_UNAUTHORIZED, ApiError::UNAUTHORIZED);
        }

        $session = Session::find(sha256($decoded));

        if ($session === null) {
            return \err(Response::HTTP_UNAUTHORIZED, ApiError::UNAUTHORIZED);
        }

        return $next($request);
    }
}
