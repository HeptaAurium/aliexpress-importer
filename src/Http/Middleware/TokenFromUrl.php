<?php

namespace Heptaaurium\AliexpressImporter\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class TokenFromUrl
{
    public function handle(Request $request, Closure $next)
    {
        if ($token = $request->query('api_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}
