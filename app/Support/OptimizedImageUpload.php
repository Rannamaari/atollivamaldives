<?php

namespace App\Support;

use Filament\Forms\Components\FileUpload;
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
