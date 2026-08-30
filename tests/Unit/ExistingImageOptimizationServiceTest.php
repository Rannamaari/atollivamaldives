<?php

namespace Tests\Unit;

use App\Models\Accommodation;
use App\Services\ExistingImageOptimizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExistingImageOptimizationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_existing_local_image_paths_to_optimized_webp_versions(): void
    {
        Storage::fake('public');

        $originalPath = UploadedFile::fake()
            ->image('resort.jpg', 3200, 2200)
            ->store('accommodations/featured', 'public');

        $accommodation = Accommodation::create([
            'type' => 'resort',
            'status' => 'published',
            'name' => 'Speedboat Bay Resort',
            'slug' => 'speedboat-bay-resort',
            'featured_image' => $originalPath,
            'published' => true,
        ]);

        $summary = app(ExistingImageOptimizationService::class)->run(dryRun: false, keepOriginals: false);

        $accommodation->refresh();

        $this->assertSame(1, $summary['updated']);
        $this->assertStringEndsWith('.webp', (string) $accommodation->featured_image);
        Storage::disk('public')->assertExists($accommodation->featured_image);
        Storage::disk('public')->assertMissing($originalPath);
    }
}
