<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJsonIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $content = $request->getContent();

        json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            abort(400, 'Bad JSON received');
        }

        return $next($request);
    }
}
