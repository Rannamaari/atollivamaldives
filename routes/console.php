<?php
use App\Console\Commands\DispatchAgencyCampaignEmails;
use App\Services\ExistingImageOptimizationService;
use App\Services\SocialImageGeneratorService;
use App\Models\Accommodation;
use App\Models\Post;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () { $this->comment('Travel made personal.'); });

Artisan::command('media:optimize-existing {--dry-run : Preview what would be optimized without changing files} {--keep-originals : Keep original files after optimized copies are created}', function (ExistingImageOptimizationService $service) {
    $dryRun = (bool) $this->option('dry-run');
    $keepOriginals = (bool) $this->option('keep-originals');

    $this->info($dryRun
        ? 'Scanning existing uploaded images in dry-run mode...'
        : 'Optimizing existing uploaded images...'
    );

    $summary = $service->run($dryRun, $keepOriginals);

    $this->table(
        ['Checked', 'Would update / Updated', 'Skipped', 'Missing', 'Failed'],
        [[
            $summary['checked'],
            $summary['updated'],
            $summary['skipped'],
            $summary['missing'],
            $summary['failed'],
        ]]
    );

    $this->newLine();
    $this->comment($dryRun
        ? 'Dry run only. No files or database paths were changed.'
        : 'Optimization complete. Existing database image paths were updated where needed.'
    );
})->purpose('Optimize existing uploaded website images and update stored paths');

Artisan::command('social:generate-images {--type=} {--missing} {--force}', function (SocialImageGeneratorService $generator) {
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
})->purpose('Generate branded social preview images for shareable Atolliva content');

Schedule::command(DispatchAgencyCampaignEmails::class)
    ->everyMinute()
    ->withoutOverlapping();
