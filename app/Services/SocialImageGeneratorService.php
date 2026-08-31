<?php

namespace App\Services;

use App\Contracts\SocialShareable;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialImageGeneratorService
{
    public function __construct(
        protected SocialShareService $socialShareService,
        protected SiteSetting $siteSetting,
    ) {
    }

    public function generateAndStore(SocialShareable $model, bool $force = false): ?string
    {
        if (! $force && filled($model->generated_social_image ?? null)) {
            return (string) $model->generated_social_image;
        }

        $sourcePath = $this->sourcePathFor($model);

        if (! $sourcePath) {
            return null;
        }

        $imageData = @getimagesize($sourcePath);

        if (! $imageData) {
            return null;
        }

        $binary = @file_get_contents($sourcePath);

        if ($binary === false) {
            return null;
        }

        $source = @imagecreatefromstring($binary);

        if (! $source) {
            return null;
        }

        $canvas = imagecreatetruecolor(1200, 630);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        $this->copyCover($source, (int) $imageData[0], (int) $imageData[1], $canvas, 1200, 630);
        $this->applyOverlay($canvas);
        $this->renderText($canvas, $model);
        $this->placeLogo($canvas);

        ob_start();
        imagewebp($canvas, null, 84);
        $binaryOutput = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        $path = 'social/'.$model->socialShareType().'/'.$model->getKey().'-'.Str::ulid().'.webp';
        Storage::disk('public')->put($path, $binaryOutput);

        $model->forceFill(['generated_social_image' => $path])->save();

        return $path;
    }

    protected function sourcePathFor(SocialShareable $model): ?string
    {
        foreach (['social_image', 'featured_image', 'generated_social_image'] as $field) {
            $value = $model->{$field} ?? null;

            if (! filled($value)) {
                continue;
            }

            if (str_starts_with((string) $value, 'http://') || str_starts_with((string) $value, 'https://')) {
                $temp = tempnam(sys_get_temp_dir(), 'atl-social-');

                if (! $temp) {
                    continue;
                }

                $context = stream_context_create([
                    'http' => ['timeout' => 6],
                    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
                ]);

                $binary = @file_get_contents((string) $value, false, $context);

                if ($binary === false) {
                    @unlink($temp);
                    continue;
                }

                file_put_contents($temp, $binary);

                return $temp;
            }

            $path = Storage::disk('public')->path(ltrim((string) $value, '/'));

            if (is_file($path)) {
                return $path;
            }
        }

        $defaultOg = $this->siteSetting->current()->default_og_image;

        if ($defaultOg && Storage::disk('public')->exists($defaultOg)) {
            return Storage::disk('public')->path($defaultOg);
        }

        return public_path('logo/optimized/atolliva-share.png');
    }

    protected function copyCover($source, int $sourceWidth, int $sourceHeight, $canvas, int $targetWidth, int $targetHeight): void
    {
        $sourceRatio = $sourceWidth / max($sourceHeight, 1);
        $targetRatio = $targetWidth / max($targetHeight, 1);

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($cropHeight * $targetRatio);
            $srcX = (int) round(($sourceWidth - $cropWidth) / 2);
            $srcY = 0;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($cropWidth / $targetRatio);
            $srcX = 0;
            $srcY = (int) round(($sourceHeight - $cropHeight) / 2);
        }

        imagecopyresampled($canvas, $source, 0, 0, $srcX, $srcY, $targetWidth, $targetHeight, $cropWidth, $cropHeight);
    }

    protected function applyOverlay($canvas): void
    {
        for ($x = 0; $x < 1200; $x++) {
            $alpha = max(18, 88 - (int) round(($x / 1200) * 60));
            $color = imagecolorallocatealpha($canvas, 7, 27, 38, $alpha);
            imageline($canvas, $x, 0, $x, 630, $color);
        }

        $bottomShade = imagecolorallocatealpha($canvas, 5, 18, 28, 82);
        imagefilledrectangle($canvas, 0, 420, 1200, 630, $bottomShade);
    }

    protected function renderText($canvas, SocialShareable $model): void
    {
        $share = $this->socialShareService->for($model);
        $title = $this->wrapText($share->title, 28, 3);
        $subtitle = $model->socialShareLocationLabel() ?: ($model->socialShareCategoryLabel() ?: 'Explore with Atolliva Maldives');
        $subtitle = Str::limit($subtitle, 54, '');
        $font = $this->fontPath();

        $white = imagecolorallocate($canvas, 255, 255, 255);
        $muted = imagecolorallocate($canvas, 210, 227, 226);

        if ($font) {
            $titleSize = $this->fontSizeForTitle($title);
            $y = 390;

            foreach (explode("\n", $title) as $line) {
                imagettftext($canvas, $titleSize, 0, 72, $y, $white, $font, $line);
                $y += (int) round($titleSize * 1.35);
            }

            imagettftext($canvas, 24, 0, 74, 520, $muted, $font, $subtitle);
            imagettftext($canvas, 20, 0, 74, 566, $muted, $font, 'Atolliva Maldives');

            return;
        }

        imagestring($canvas, 5, 72, 430, str_replace("\n", ' ', $title), $white);
        imagestring($canvas, 4, 72, 490, $subtitle, $muted);
        imagestring($canvas, 4, 72, 520, 'Atolliva Maldives', $muted);
    }

    protected function placeLogo($canvas): void
    {
        $logoPath = public_path('logo/optimized/atolliva-logo.png');

        if (! is_file($logoPath)) {
            return;
        }

        $logoBinary = @file_get_contents($logoPath);

        if ($logoBinary === false) {
            return;
        }

        $logo = @imagecreatefromstring($logoBinary);

        if (! $logo) {
            return;
        }

        $logoWidth = imagesx($logo);
        $logoHeight = imagesy($logo);
        $targetWidth = 180;
        $targetHeight = (int) round(($logoHeight / max($logoWidth, 1)) * $targetWidth);

        imagecopyresampled($canvas, $logo, 950, 58, 0, 0, $targetWidth, $targetHeight, $logoWidth, $logoHeight);
        imagedestroy($logo);
    }

    protected function wrapText(string $text, int $maxLineLength, int $maxLines): string
    {
        $wrapped = wordwrap(Str::upper(Str::of($text)->replace(' | Atolliva Maldives', '')->value()), $maxLineLength, "\n", true);
        $lines = array_slice(array_filter(explode("\n", $wrapped)), 0, $maxLines);

        return implode("\n", $lines);
    }

    protected function fontPath(): ?string
    {
        foreach ([
            resource_path('fonts/AtollivaSocial.ttf'),
            '/System/Library/Fonts/Supplemental/Arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/dejavu/DejaVuSans.ttf',
        ] as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    protected function fontSizeForTitle(string $title): int
    {
        $lineCount = max(1, substr_count($title, "\n") + 1);

        return match (true) {
            $lineCount >= 3 => 42,
            $lineCount === 2 => 52,
            default => 62,
        };
    }
}
