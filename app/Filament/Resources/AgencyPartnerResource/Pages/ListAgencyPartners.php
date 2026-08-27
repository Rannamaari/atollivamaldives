<?php

namespace App\Filament\Resources\AgencyPartnerResource\Pages;

use App\Filament\Resources\AgencyPartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgencyPartners extends ListRecords
{
    protected static string $resource = AgencyPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New agency partner'),
        ];
    }
}
