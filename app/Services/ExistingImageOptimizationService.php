<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\AccommodationImage;
use App\Models\Atoll;
use App\Models\BlogOffer;
use App\Models\HomePage;
use App\Models\Island;
use App\Models\LiveaboardPage;
use App\Models\Post;
use App\Models\RoomImage;
use App\Models\SiteSetting;
use App\Support\ImageOptimizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExistingImageOptimizationService
{
    public function __construct(
        protected ImageOptimizer $optimizer,
    ) {
    }

    public function run(bool $dryRun = true, bool $keepOriginals = false): array
    {
        $summary = [
            'checked' => 0,
            'updated' => 0,
            'skipped' => 0,
            'missing' => 0,
            'failed' => 0,
        ];

        foreach ($this->definitions() as $definition) {
            $definition['model']::query()->chunkById(100, function ($records) use ($definition, $dryRun, $keepOriginals, &$summary): void {
                foreach ($records as $record) {
                    foreach ($definition['fields'] as $field => $options) {
                        $summary = $this->processField($record, $field, $options, $summary, $dryRun, $keepOriginals);
                    }
                }
            });
        }

        return $summary;
    }

    protected function processField(Model $record, string $field, array $options, array $summary, bool $dryRun, bool $keepOriginals): array
    {
        $value = $record->getAttribute($field);

        if (is_array($value)) {
            $updated = false;
            $newValues = [];

            foreach ($value as $item) {
                $summary['checked']++;
                $result = $this->optimizeValue($item, $options, $dryRun, $keepOriginals);
                $summary[$result['status']]++;
                $newValues[] = $result['path'];
                $updated = $updated || $result['updated'];
            }

            if ($updated && ! $dryRun) {
                $record->forceFill([$field => $newValues])->save();
            }

            return $summary;
        }

        if (! is_string($value) || blank($value)) {
            return $summary;
        }

        $summary['checked']++;
        $result = $this->optimizeValue($value, $options, $dryRun, $keepOriginals);
        $summary[$result['status']]++;

        if ($result['updated'] && ! $dryRun) {
            $record->forceFill([$field => $result['path']])->save();
        }

        return $summary;
    }

    protected function optimizeValue(string $path, array $options, bool $dryRun, bool $keepOriginals): array
    {
        if ($this->shouldSkip($path)) {
            return ['status' => 'skipped', 'path' => $path, 'updated' => false];
        }

        $normalizedPath = ltrim($path, '/');

        if (! Storage::disk('public')->exists($normalizedPath)) {
            return ['status' => 'missing', 'path' => $path, 'updated' => false];
        }

        if ($dryRun) {
            return ['status' => 'updated', 'path' => $path, 'updated' => true];
        }

        $optimizedPath = $this->optimizer->optimizeStoredPath(
            path: $normalizedPath,
            disk: 'public',
            targetDirectory: $options['directory'] ?? null,
            maxWidth: $options['maxWidth'] ?? 2200,
            maxHeight: $options['maxHeight'] ?? 2200,
            quality: $options['quality'] ?? 82,
        );

        if (! $optimizedPath) {
            return ['status' => 'failed', 'path' => $path, 'updated' => false];
        }

        if (! $keepOriginals && Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }

        return ['status' => 'updated', 'path' => $optimizedPath, 'updated' => true];
    }

    protected function shouldSkip(string $path): bool
    {
        return blank($path)
            || str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')
            || str_starts_with($path, 'placeholders/')
            || str_ends_with(strtolower($path), '.svg');
    }

    protected function definitions(): array
    {
        return [
            [
                'model' => Accommodation::class,
                'fields' => [
                    'featured_image' => ['directory' => 'accommodations/featured', 'maxWidth' => 2000, 'maxHeight' => 1400, 'quality' => 82],
                    'images' => ['directory' => 'accommodations', 'maxWidth' => 2000, 'maxHeight' => 1400, 'quality' => 82],
                ],
            ],
            [
                'model' => Post::class,
                'fields' => [
                    'featured_image' => ['directory' => 'blog', 'maxWidth' => 1800, 'maxHeight' => 1200, 'quality' => 82],
                ],
            ],
            [
                'model' => BlogOffer::class,
                'fields' => [
                    'image' => ['directory' => 'blog-offers', 'maxWidth' => 1600, 'maxHeight' => 1200, 'quality' => 82],
                ],
            ],
            [
                'model' => HomePage::class,
                'fields' => [
                    'hero_image' => ['directory' => 'home-pages', 'maxWidth' => 2200, 'maxHeight' => 1600, 'quality' => 82],
                    'resorts_card_image' => ['directory' => 'home-pages/cards', 'maxWidth' => 1400, 'maxHeight' => 1000, 'quality' => 80],
                    'guesthouses_card_image' => ['directory' => 'home-pages/cards', 'maxWidth' => 1400, 'maxHeight' => 1000, 'quality' => 80],
                    'city_hotels_card_image' => ['directory' => 'home-pages/cards', 'maxWidth' => 1400, 'maxHeight' => 1000, 'quality' => 80],
                    'liveaboards_card_image' => ['directory' => 'home-pages/cards', 'maxWidth' => 1400, 'maxHeight' => 1000, 'quality' => 80],
                ],
            ],
            [
                'model' => LiveaboardPage::class,
                'fields' => [
                    'hero_image' => ['directory' => 'liveaboards/hero', 'maxWidth' => 2200, 'maxHeight' => 1600, 'quality' => 82],
                    'gallery_images' => ['directory' => 'liveaboards/gallery', 'maxWidth' => 2000, 'maxHeight' => 1400, 'quality' => 82],
                ],
            ],
            [
                'model' => SiteSetting::class,
                'fields' => [
                    'hero_image' => ['directory' => 'site', 'maxWidth' => 2200, 'maxHeight' => 1600, 'quality' => 82],
                    'default_og_image' => ['directory' => 'site/seo', 'maxWidth' => 1600, 'maxHeight' => 1600, 'quality' => 82],
                    'business_logo' => ['directory' => 'site/branding', 'maxWidth' => 1200, 'maxHeight' => 1200, 'quality' => 86],
                ],
            ],
            [
                'model' => Atoll::class,
                'fields' => [
                    'featured_image' => ['directory' => 'destinations/atolls', 'maxWidth' => 1800, 'maxHeight' => 1200, 'quality' => 82],
                ],
            ],
            [
                'model' => Island::class,
                'fields' => [
                    'featured_image' => ['directory' => 'destinations/islands', 'maxWidth' => 1800, 'maxHeight' => 1200, 'quality' => 82],
                ],
            ],
            [
                'model' => AccommodationImage::class,
                'fields' => [
                    'image_path' => ['directory' => 'accommodations', 'maxWidth' => 2000, 'maxHeight' => 1400, 'quality' => 82],
                ],
            ],
            [
                'model' => RoomImage::class,
                'fields' => [
                    'image_path' => ['directory' => 'rooms', 'maxWidth' => 1800, 'maxHeight' => 1200, 'quality' => 82],
                ],
            ],
        ];
    }
}
