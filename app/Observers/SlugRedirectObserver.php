<?php

namespace App\Observers;

use App\Models\SeoRedirect;
use Illuminate\Database\Eloquent\Model;

class SlugRedirectObserver
{
    protected static array $oldPaths = [];

    public function updating(Model $model): void
    {
        if (! $model->isDirty('slug') || ! method_exists($model, 'publicPathForSlug')) {
            return;
        }

        static::$oldPaths[spl_object_id($model)] = $model->publicPathForSlug($model->getOriginal('slug'));
    }

    public function updated(Model $model): void
    {
        if (! $model->wasChanged('slug') || ! method_exists($model, 'publicPathForSlug')) {
            return;
        }

        $objectId = spl_object_id($model);
        $oldPath = static::$oldPaths[$objectId] ?? null;
        $newPath = $model->publicPathForSlug($model->slug);

        unset(static::$oldPaths[$objectId]);

        if (! filled($oldPath) || ! filled($newPath)) {
            return;
        }

        SeoRedirect::storePermanent($oldPath, $newPath);
    }
}
