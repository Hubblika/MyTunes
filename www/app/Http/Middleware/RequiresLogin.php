<?php

namespace App\Http\Middleware;

use App\Models\Session;
use Closure;
use ApiError;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RequiresLogin {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response {
        $token = $request->cookie('mytunes_auth_token');

        Log::info('Cookies:', $request->cookies->all());

        if ($token === null) {
            return $this->unauthorizedResponse($request);
        }

        $decoded = base64_decode($token, true);

        if (!$decoded) {
            return $this->unauthorizedResponse($request);
        }

        $session = Session::find(sha256($decoded));

        if ($session === null) {
            return $this->unauthorizedResponse($request);
        }

        return $next($request);
    }

    private function unauthorizedResponse(Request $request) {
        if($request->expectsJson()) {
            return \err(Response::HTTP_UNAUTHORIZED, ApiError::UNAUTHORIZED);
        }

        return redirect('login');
    }
}
