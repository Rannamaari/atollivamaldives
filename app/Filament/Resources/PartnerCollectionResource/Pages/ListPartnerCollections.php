<?php

namespace App\Filament\Resources\PartnerCollectionResource\Pages;

use App\Filament\Resources\PartnerCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerCollections extends ListRecords
{
    protected static string $resource = PartnerCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New folder / list'),
        ];
    }
}
