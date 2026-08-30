<?php
use Illuminate\Support\Facades\Artisan;
use App\Services\ExistingImageOptimizationService;

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
