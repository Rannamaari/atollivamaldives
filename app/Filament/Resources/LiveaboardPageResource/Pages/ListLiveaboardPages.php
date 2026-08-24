<?php

namespace App\Filament\Resources\LiveaboardPageResource\Pages;

use App\Filament\Resources\LiveaboardPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLiveaboardPages extends ListRecords
{
    protected static string $resource = LiveaboardPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn (): bool => static::getResource()::getModel()::query()->count() === 0),
        ];
    }
}
