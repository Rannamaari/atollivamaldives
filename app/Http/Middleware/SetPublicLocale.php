<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    public function handle(Request $request, Closure $next, string $locale = 'en'): Response
    {
        app()->setLocale($locale);

        view()->share('documentLocale', $locale);

        return $next($request);
    }
}
