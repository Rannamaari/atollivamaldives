<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoRedirect extends Model
{
    protected $fillable = [
        'source_path',
        'destination_path',
        'http_status',
        'active',
        'hits',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'http_status' => 'integer',
            'hits' => 'integer',
        ];
    }

    public static function normalizePath(?string $path): string
    {
        $normalized = '/'.ltrim((string) $path, '/');

        return $normalized === '/' ? $normalized : rtrim($normalized, '/');
    }

    public static function findActiveForPath(?string $path): ?self
    {
        return static::query()
            ->where('active', true)
            ->where('source_path', static::normalizePath($path))
            ->first();
    }

    public static function storePermanent(string $sourcePath, string $destinationPath, int $httpStatus = 301): ?self
    {
        $sourcePath = static::normalizePath($sourcePath);
        $destinationPath = static::normalizePath($destinationPath);

        if ($sourcePath === $destinationPath) {
            return null;
        }

        return static::query()->updateOrCreate(
            ['source_path' => $sourcePath],
            [
                'destination_path' => $destinationPath,
                'http_status' => $httpStatus,
                'active' => true,
            ],
        );
    }
}
