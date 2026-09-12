<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\LiveaboardPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArabicLiveaboardLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_arabic_liveaboard_landing_uses_approved_page_and_boat_copy(): void
    {
        LiveaboardPage::query()->firstOrFail()->update([
            'eyebrow' => 'LIVEABOARD MALDIVES',
            'title' => 'English liveaboard title',
            'intro' => 'English landing page introduction.',
            'body' => '<p>English landing page story.</p>',
            'contact_heading' => 'Plan your journey',
            'contact_text' => 'English contact copy.',
            'arabic_eyebrow' => 'رحلات القوارب في المالديف',
            'arabic_title' => 'رحلتك البحرية في المالديف',
            'arabic_intro' => 'مقدمة عربية لرحلة القارب.',
            'arabic_body' => '<p>قصة عربية لرحلة القارب.</p>',
            'arabic_contact_heading' => 'خطط لرحلتك البحرية',
            'arabic_contact_text' => 'نص عربي للتواصل.',
        ]);

        Accommodation::create([
            'type' => 'liveaboard',
            'status' => 'published',
            'name' => 'English Boat',
            'slug' => 'english-boat',
            'summary' => 'English boat summary.',
            'arabic_name' => 'القارب العربي',
            'arabic_summary' => 'ملخص عربي للقارب.',
            'published' => true,
        ]);

        $this->get(route('arabic.liveaboards.index'))
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('رحلتك البحرية في المالديف')
            ->assertSee('قصة عربية لرحلة القارب.', false)
            ->assertSee('القارب العربي')
            ->assertSee('ملخص عربي للقارب.')
            ->assertSee('<link rel="canonical" href="'.route('arabic.liveaboards.index').'">', false)
            ->assertSee('hreflang="en"', false);
    }
}
