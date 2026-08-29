<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReCaptchaVerifier
{
    public function enabled(): bool
    {
        return config('services.recaptcha.enabled')
            && filled(config('services.recaptcha.site_key'))
            && filled(config('services.recaptcha.secret_key'));
    }

    public function verify(string $token, string $action, ?string $ip = null): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post('https://www.google.com/recaptcha/api/siteverify', array_filter([
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ], fn ($value) => filled($value)))
            ->json();

        if (! is_array($response) || ! ($response['success'] ?? false)) {
            return false;
        }

        if (($response['action'] ?? null) !== $action) {
            return false;
        }

        return (float) ($response['score'] ?? 0) >= (float) config('services.recaptcha.min_score', 0.5);
    }
}
