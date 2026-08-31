<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureMarketingAttribution
{
    public function handle(Request $request, Closure $next): Response
    {
        $keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content'];
        $incoming = collect($keys)
            ->mapWithKeys(fn (string $key): array => [$key => $request->query($key)])
            ->filter(fn ($value) => filled($value))
            ->all();

        if ($incoming !== [] && ! $request->session()->has('marketing_attribution')) {
            $request->session()->put('marketing_attribution', [
                ...$incoming,
                'landing_page' => $request->fullUrlWithoutQuery($keys),
            ]);
        }

        return $next($request);
    }
}
