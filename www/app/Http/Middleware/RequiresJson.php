<?php

namespace App\Http\Middleware;

use Closure;
use HttpError;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $contentType = $request->header('content-type');
        $needle = 'application/json';

        if ($contentType === null || !str_starts_with($contentType, $needle)) {
            \err(Response::HTTP_BAD_REQUEST, HttpError::INVALID_CONTENT_TYPE);
        }

        return $next($request);
    }
}
