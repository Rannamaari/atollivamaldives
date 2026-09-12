<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArabicAccommodationTest extends TestCase
{
    use RefreshDatabase;

    public function test_translated_resort_uses_arabic_content_and_metadata_on_its_arabic_url(): void
    {
        $resort = Accommodation::create([
            'type' => 'resort',
            'status' => 'published',
            'name' => 'Baros Maldives',
            'slug' => 'baros-maldives',
            'summary' => 'An English resort summary.',
            'description' => '<p>English resort description.</p>',
            'arabic_name' => 'باروس المالديف',
            'arabic_summary' => 'ملخص عربي للمنتجع.',
            'arabic_description' => '<p>وصف عربي للمنتجع.</p>',
            'published' => true,
        ]);

        $this->get(route('resorts.show', $resort))
            ->assertOk()
            ->assertSee(route('arabic.resorts.show', $resort), false)
            ->assertSee('hreflang="ar"', false);

        $this->get(route('arabic.resorts.show', $resort))
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false)
            ->assertSee($resort->arabic_name)
            ->assertSee($resort->arabic_summary)
            ->assertSee('وصف عربي للمنتجع.', false)
            ->assertSee('اطلب السعر والتوفر')
            ->assertSee('أخبرنا عن')
            ->assertSee('خيارات')
            ->assertDontSee('REQUEST PRICE & AVAILABILITY')
            ->assertSee('مالديفك، مخططة بعناية')
            ->assertSee(route('arabic.resorts.index'), false)
            ->assertSee('<link rel="canonical" href="'.route('arabic.resorts.show', $resort).'">', false)
            ->assertSee('hreflang="en"', false);
    }

    public function test_arabic_product_listing_uses_the_translated_card_copy_when_available(): void
    {
        Accommodation::create([
            'type' => 'package',
            'status' => 'published',
            'name' => 'English Maldives Package',
            'slug' => 'english-maldives-package',
            'property_subtype' => 'family',
            'summary' => 'English package summary.',
            'arabic_name' => 'باقة المالديف العربية',
            'arabic_summary' => 'ملخص عربي للباقة.',
            'published' => true,
        ]);

        $this->get(route('arabic.packages.index'))
            ->assertOk()
            ->assertSee('باقة المالديف العربية')
            ->assertSee('ملخص عربي للباقة.')
            ->assertSee('<link rel="canonical" href="'.route('arabic.packages.index').'">', false);
    }

    public function test_arabic_listing_uses_arabic_empty_results_copy(): void
    {
        $this->get(route('arabic.resorts.index', ['destination' => 'No matching island']))
            ->assertOk()
            ->assertSee('لا توجد خيارات مطابقة حالياً.')
            ->assertSee('جرّب توسيع وجهتك أو خيارات نوع الإقامة');
    }
}
