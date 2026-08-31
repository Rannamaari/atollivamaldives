<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use App\Models\SocialShareVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $response = $next($request);

        $settings = SiteSetting::current();

        if (
            $settings->enable_share_tracking
            && ($incoming['utm_medium'] ?? null) === 'social'
            && filled($incoming['utm_source'] ?? null)
        ) {
            $landingPage = $request->fullUrlWithoutQuery($keys);
            $sessionId = $request->session()->getId();
            $visitKey = hash('sha256', implode('|', [
                $sessionId,
                (string) ($incoming['utm_source'] ?? ''),
                (string) ($incoming['utm_medium'] ?? ''),
                (string) ($incoming['utm_campaign'] ?? ''),
                (string) ($incoming['utm_content'] ?? ''),
                $landingPage,
            ]));

            SocialShareVisit::query()->firstOrCreate(
                ['visit_key' => $visitKey],
                [
                    'session_id' => $sessionId,
                    'utm_source' => $incoming['utm_source'] ?? null,
                    'utm_medium' => $incoming['utm_medium'] ?? null,
                    'utm_campaign' => $incoming['utm_campaign'] ?? null,
                    'utm_content' => $incoming['utm_content'] ?? null,
                    'landing_page' => $landingPage,
                    'path' => $request->path(),
                    'user_id' => Auth::id(),
                    'ip_hash' => $request->ip() ? hash('sha256', $request->ip()) : null,
                    'user_agent' => $request->userAgent(),
                    'referrer' => $request->headers->get('referer'),
                ]
            );
        }

        return $response;
    }
}
