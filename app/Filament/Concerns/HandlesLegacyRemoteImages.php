<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HandlesLegacyRemoteImages
{
    protected function nullifyLegacyRemoteImageFields(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (isset($data[$field]) && is_string($data[$field]) && $this->isRemoteImagePath($data[$field])) {
                $data[$field] = null;
            }
        }

        return $data;
    }

    protected function restoreLegacyRemoteImageFields(array $data, Model $record, array $fields): array
    {
        foreach ($fields as $field) {
            $current = $record->getAttribute($field);

            if (
                blank($data[$field] ?? null)
                && is_string($current)
                && $this->isRemoteImagePath($current)
            ) {
                $data[$field] = $current;
            }
        }

        return $data;
    }

    protected function isRemoteImagePath(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}
