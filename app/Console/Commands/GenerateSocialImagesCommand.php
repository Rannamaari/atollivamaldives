<?php

namespace App\Console\Commands;

use App\Models\Accommodation;
use App\Models\Post;
use App\Services\SocialImageGeneratorService;
use Illuminate\Console\Command;

class GenerateSocialImagesCommand extends Command
{
    protected $signature = 'social:generate-images {--type=} {--missing} {--force}';

    protected $description = 'Generate branded social preview images for shareable Atolliva content.';

    public function handle(SocialImageGeneratorService $generator): int
    {
        $type = (string) $this->option('type');
        $missingOnly = (bool) $this->option('missing');
        $force = (bool) $this->option('force');
        $generated = 0;

        $queryModels = [
            'post' => Post::query(),
            'resort' => Accommodation::query()->where('type', 'resort'),
            'guesthouse' => Accommodation::query()->where('type', 'guesthouse'),
            'liveaboard' => Accommodation::query()->where('type', 'liveaboard'),
            'package' => Accommodation::query()->where('type', 'package'),
            'city_hotel' => Accommodation::query()->where('type', 'city_hotel'),
        ];

        foreach ($queryModels as $key => $query) {
            if ($type !== '' && $type !== $key) {
                continue;
            }

            if ($missingOnly) {
                $query->whereNull('generated_social_image');
            }

            $query->chunkById(100, function ($records) use ($generator, $force, &$generated): void {
                foreach ($records as $record) {
                    if ($generator->generateAndStore($record, $force)) {
                        $generated++;
                    }
                }
            });
        }

        $this->info("Generated {$generated} social image(s).");

        return self::SUCCESS;
    }
}
