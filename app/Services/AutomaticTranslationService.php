<?php

namespace App\Services;

use App\Models\AutomaticTranslation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AutomaticTranslationService
{
    public function translate(string $text, string $targetLocale = 'ar', bool $isHtml = false): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        $sourceHash = hash('sha256', implode('|', ['en', $targetLocale, $isHtml ? 'html' : 'text', $text]));
        $cached = AutomaticTranslation::query()
            ->where('source_hash', $sourceHash)
            ->value('translated_text');

        if (filled($cached)) {
            return $cached;
        }

        $apiKey = config('services.google_translate.api_key');

        if (! filled($apiKey)) {
            throw new RuntimeException('Google Cloud Translation is not configured. Add GOOGLE_TRANSLATE_API_KEY to the .env file first.');
        }

        $response = Http::acceptJson()
            ->timeout((int) config('services.google_translate.timeout', 15))
            ->post('https://translation.googleapis.com/language/translate/v2?key='.rawurlencode($apiKey), [
                'q' => $text,
                'source' => 'en',
                'target' => $targetLocale,
                'format' => $isHtml ? 'html' : 'text',
            ]);

        if ($response->failed()) {
            $message = trim((string) data_get($response->json(), 'error.message'));

            Log::warning('Google Cloud Translation request failed.', [
                'status' => $response->status(),
                'message' => $message,
            ]);

            throw new RuntimeException(
                'Google Cloud Translation rejected the request (HTTP '.$response->status().').'
                .($message !== '' ? ' '.$message : '')
            );
        }

        $translatedText = html_entity_decode((string) data_get($response->json(), 'data.translations.0.translatedText'), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (trim($translatedText) === '') {
            throw new RuntimeException('Google Cloud Translation returned an empty draft. Please try again later.');
        }

        AutomaticTranslation::query()->updateOrCreate(
            ['source_hash' => $sourceHash],
            [
                'source_locale' => 'en',
                'target_locale' => $targetLocale,
                'source_text' => $text,
                'translated_text' => $translatedText,
                'provider' => 'google-cloud-translation',
            ],
        );

        return $translatedText;
    }
}
