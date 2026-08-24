<?php

namespace App\Filament\Resources\LiveaboardResource\Pages;

use App\Filament\Resources\LiveaboardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLiveaboards extends ListRecords
{
    protected static string $resource = LiveaboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
