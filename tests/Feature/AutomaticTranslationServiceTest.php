<?php

namespace Tests\Feature;

use App\Models\AutomaticTranslation;
use App\Services\AutomaticTranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AutomaticTranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_translates_once_and_reuses_the_cached_draft(): void
    {
        config()->set('services.google_translate.api_key', 'test-key');

        Http::fake([
            'translation.googleapis.com/*' => Http::response([
                'data' => ['translations' => [['translatedText' => 'مرحبا بالمالديف']]],
            ]),
        ]);

        $translator = app(AutomaticTranslationService::class);

        $this->assertSame('مرحبا بالمالديف', $translator->translate('Welcome to the Maldives'));
        $this->assertSame('مرحبا بالمالديف', $translator->translate('Welcome to the Maldives'));
        $this->assertSame(1, AutomaticTranslation::count());
        Http::assertSentCount(1);
    }
}
