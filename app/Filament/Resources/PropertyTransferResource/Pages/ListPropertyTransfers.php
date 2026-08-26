<?php

namespace App\Filament\Resources\PropertyTransferResource\Pages;

use App\Filament\Resources\PropertyTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyTransfers extends ListRecords
{
    protected static string $resource = PropertyTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
