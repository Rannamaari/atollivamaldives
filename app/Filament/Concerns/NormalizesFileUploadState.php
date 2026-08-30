<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Str;

trait NormalizesFileUploadState
{
    public function getFormUploadedFiles(string $statePath): ?array
    {
        $this->normalizeLegacyUploadStateFor($statePath);

        return parent::getFormUploadedFiles($statePath);
    }

    public function removeFormUploadedFile(string $statePath, string $fileKey): void
    {
        $this->normalizeLegacyUploadStateFor($statePath);

        parent::removeFormUploadedFile($statePath, $fileKey);
    }

    public function reorderFormUploadedFiles(string $statePath, array $fileKeys): void
    {
        $this->normalizeLegacyUploadStateFor($statePath);

        parent::reorderFormUploadedFiles($statePath, $fileKeys);
    }

    protected function normalizeLegacyUploadStateFor(string $statePath): void
    {
        if (! in_array($statePath, $this->legacyUploadStatePaths(), true)) {
            return;
        }

        $state = data_get($this, $statePath);

        if (! is_string($state)) {
            return;
        }

        if (str_starts_with($state, 'http://') || str_starts_with($state, 'https://')) {
            data_set($this, $statePath, null);

            return;
        }

        data_set($this, $statePath, [
            (string) Str::uuid() => $state,
        ]);
    }

    /**
     * @return array<int, string>
     */
    abstract protected function legacyUploadStatePaths(): array;
}
