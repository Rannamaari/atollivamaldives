<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    public function store(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxWidth = 2200,
        int $maxHeight = 2200,
        int $quality = 82,
    ): string {
        $image = $this->makeImage($file);

        if (! $image) {
            return $file->storeAs($directory, Str::ulid().'.'.$file->getClientOriginalExtension(), $disk);
        }

        [$source, $width, $height] = $image;
        [$targetWidth, $targetHeight] = $this->fitDimensions($width, $height, $maxWidth, $maxHeight);

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height,
        );

        ob_start();
        imagewebp($canvas, null, $quality);
        $binary = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        $path = trim($directory, '/').'/'.Str::ulid().'.webp';
        Storage::disk($disk)->put($path, $binary);

        return $path;
    }

    public function optimizeStoredPath(
        string $path,
        string $disk = 'public',
        ?string $targetDirectory = null,
        int $maxWidth = 2200,
        int $maxHeight = 2200,
        int $quality = 82,
    ): ?string {
        $normalizedPath = ltrim($path, '/');

        if (! Storage::disk($disk)->exists($normalizedPath)) {
            return null;
        }

        $absolutePath = Storage::disk($disk)->path($normalizedPath);
        $image = $this->makeImageFromPath($absolutePath);

        if (! $image) {
            return null;
        }

        [$source, $width, $height] = $image;
        [$targetWidth, $targetHeight] = $this->fitDimensions($width, $height, $maxWidth, $maxHeight);

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height,
        );

        ob_start();
        imagewebp($canvas, null, $quality);
        $binary = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        $directory = trim($targetDirectory ?: dirname($normalizedPath), '/.');
        $optimizedPath = ($directory ? $directory.'/' : '').Str::ulid().'.webp';
        Storage::disk($disk)->put($optimizedPath, $binary);

        return $optimizedPath;
    }

    protected function makeImage(UploadedFile $file): ?array
    {
        $realPath = $file->getRealPath();

        if (! $realPath || ! is_file($realPath)) {
            return null;
        }

        return $this->makeImageFromPath($realPath);
    }

    protected function makeImageFromPath(string $realPath): ?array
    {
        if (! is_file($realPath)) {
            return null;
        }

        $imageInfo = @getimagesize($realPath);

        if (! $imageInfo) {
            return null;
        }

        [$width, $height] = $imageInfo;
        $mime = $imageInfo['mime'] ?? null;
        $binary = @file_get_contents($realPath);

        if ($binary === false) {
            return null;
        }

        $source = match ($mime) {
            'image/jpeg', 'image/png', 'image/webp' => @imagecreatefromstring($binary),
            default => null,
        };

        if (! $source) {
            return null;
        }

        return [$source, $width, $height];
    }

    protected function fitDimensions(int $width, int $height, int $maxWidth, int $maxHeight): array
    {
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return [$width, $height];
        }

        $ratio = min($maxWidth / max($width, 1), $maxHeight / max($height, 1));

        return [
            max(1, (int) round($width * $ratio)),
            max(1, (int) round($height * $ratio)),
        ];
    }
}
