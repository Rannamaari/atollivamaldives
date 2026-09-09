<?php

namespace Tests\Unit;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccommodationPackageOfferTest extends TestCase
{
    #[Test]
    public function expired_packages_are_not_currently_published(): void
    {
        Carbon::setTestNow('2026-09-09');

        $package = new Accommodation([
            'type' => AccommodationType::Package,
            'published' => true,
            'offer_starts_on' => '2026-08-01',
            'offer_ends_on' => '2026-09-08',
        ]);

        $this->assertFalse($package->isCurrentlyPublished());
    }

    #[Test]
    public function an_active_scheduled_price_replaces_the_standard_package_price(): void
    {
        Carbon::setTestNow('2026-09-09');

        $package = new Accommodation([
            'type' => AccommodationType::Package,
            'price_from' => 1000,
            'currency' => 'USD',
            'package_price_periods' => [
                ['label' => 'September offer', 'starts_on' => '2026-09-01', 'ends_on' => '2026-09-30', 'price' => 850, 'currency' => 'USD'],
                ['label' => 'Festive offer', 'starts_on' => '2026-12-20', 'ends_on' => '2026-12-31', 'price' => 1200, 'currency' => 'USD'],
            ],
        ]);

        $this->assertSame('September offer', $package->activePackagePricePeriod()['label']);
        $this->assertSame(850, $package->currentDisplayPrice());
        $this->assertSame('USD', $package->currentDisplayCurrency());
    }

    #[Test]
    public function package_guest_inclusions_and_work_permit_audiences_are_human_readable(): void
    {
        $package = new Accommodation([
            'type' => AccommodationType::Package,
            'package_included_adults' => 2,
            'package_included_children' => 1,
            'package_children_free' => true,
            'eligible_audiences' => ['locals', 'expats'],
        ]);

        $this->assertSame('2 Pax + 1 Child Free', $package->packageGuestInclusionLabel());
        $this->assertSame(['Maldives locals', 'Work permit holders'], $package->packageAudienceLabels());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
