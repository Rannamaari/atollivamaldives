<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait TracksUserstamps
{
    public static function bootTracksUserstamps(): void
    {
        static::creating(function (Model $model): void {
            if (! auth()->check()) {
                return;
            }

            $userId = auth()->id();

            if (blank($model->getAttribute('created_by'))) {
                $model->setAttribute('created_by', $userId);
            }

            if (blank($model->getAttribute('updated_by'))) {
                $model->setAttribute('updated_by', $userId);
            }
        });

        static::updating(function (Model $model): void {
            if (auth()->check()) {
                $model->setAttribute('updated_by', auth()->id());
            }
        });
    }
}
