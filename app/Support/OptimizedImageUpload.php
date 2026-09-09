<?php

namespace App\Support;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class OptimizedImageUpload
{
    public static function make(
        FileUpload $upload,
        string $directory,
        int $maxWidth = 2200,
        int $maxHeight = 2200,
        int $quality = 82,
    ): FileUpload {
        return $upload
            ->image()
            ->disk('public')
            ->directory($directory)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(15360)
            ->getUploadedFileUsing(static function (BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array {
                $storage = $component->getDisk();

                try {
                    if (! $storage->exists($file)) {
                        return null;
                    }
                } catch (UnableToCheckFileExistence) {
                    return null;
                }

                return [
                    'name' => ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
                    'size' => $storage->size($file),
                    'type' => $storage->mimeType($file),
                    // FilePond fetches this URL itself to render a saved preview.
                    'url' => route('admin.media-preview', ['path' => $file]),
                ];
            })
            ->saveUploadedFileUsing(
                fn (TemporaryUploadedFile $file): string => app(ImageOptimizer::class)->store(
                    file: $file,
                    directory: $directory,
                    disk: 'public',
                    maxWidth: $maxWidth,
                    maxHeight: $maxHeight,
                    quality: $quality,
                )
            );
    }
}
