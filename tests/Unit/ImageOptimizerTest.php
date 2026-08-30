<?php

namespace Tests\Unit;

use App\Support\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_converts_uploaded_images_to_optimized_webp_files(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('hero.jpg', 4000, 2500);

        $path = app(ImageOptimizer::class)->store(
            file: $file,
            directory: 'optimized-tests',
            disk: 'public',
            maxWidth: 1800,
            maxHeight: 1200,
            quality: 82,
        );

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        [$width, $height, $type] = getimagesize(Storage::disk('public')->path($path));

        $this->assertLessThanOrEqual(1800, $width);
        $this->assertLessThanOrEqual(1200, $height);
        $this->assertSame(IMAGETYPE_WEBP, $type);
    }
}
