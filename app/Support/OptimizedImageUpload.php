<?php

namespace App\Support;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

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
            // The optimized file is already stored at this point. Avoid a second
            // filesystem metadata request, which can leave FilePond loading forever.
            ->fetchFileInformation(false)
            ->getUploadedFileUsing(static function (BaseFileUpload $component, string $file, string|array|null $storedFileNames): array {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $storedFileName = $component->isMultiple() && is_array($storedFileNames)
                    ? ($storedFileNames[$file] ?? null)
                    : $storedFileNames;

                // FilePond needs a real size to finish its preview state. Reading the
                // local optimized file avoids the slower storage metadata calls.
                $size = 1;

                try {
                    $path = $component->getDisk()->path($file);
                    $size = (is_file($path) ? filesize($path) : false) ?: 1;
                } catch (Throwable) {
                    // The saved image remains usable through its public URL below.
                }

                return [
                    'name' => $storedFileName ?? basename($file),
                    'size' => $size,
                    'type' => match ($extension) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        default => 'image/webp',
                    },
                    'url' => $component->getDisk()->url($file),
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
