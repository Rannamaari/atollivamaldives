<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Concerns\HandlesLegacyRemoteImages;
use App\Filament\Concerns\NormalizesFileUploadState;
use Filament\Resources\Pages\EditRecord;

abstract class EditTravelProduct extends EditRecord
{
    use HandlesLegacyRemoteImages;
    use NormalizesFileUploadState;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = $this->nullifyLegacyRemoteImageFields($data, [
            'featured_image',
            'social_image',
        ]);

        // Older seeded products can have a remote gallery URL. Public pages may
        // use it, but FilePond expects files stored on the configured disk.
        if (is_array($data['images'] ?? null)) {
            $data['images'] = array_values(array_filter(
                $data['images'],
                fn (mixed $path): bool => ! is_string($path) || ! $this->isRemoteImagePath($path),
            ));
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->restoreLegacyRemoteImageFields($data, $this->getRecord(), [
            'featured_image',
            'social_image',
        ]);

        $remoteGalleryImages = collect($this->getRecord()->images ?? [])
            ->filter(fn (mixed $path): bool => is_string($path) && $this->isRemoteImagePath($path))
            ->values()
            ->all();

        if ($remoteGalleryImages !== []) {
            $uploadedImages = is_array($data['images'] ?? null) ? $data['images'] : [];
            $data['images'] = array_values(array_unique([...$remoteGalleryImages, ...$uploadedImages]));
        }

        return $data;
    }

    protected function legacyUploadStatePaths(): array
    {
        return [
            'data.featured_image',
            'data.images',
            'data.social_image',
        ];
    }
}
