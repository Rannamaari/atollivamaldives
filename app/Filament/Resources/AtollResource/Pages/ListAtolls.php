<?php

namespace App\Filament\Resources\AtollResource\Pages;

use App\Filament\Resources\AtollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtolls extends ListRecords
{
    protected static string $resource = AtollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
