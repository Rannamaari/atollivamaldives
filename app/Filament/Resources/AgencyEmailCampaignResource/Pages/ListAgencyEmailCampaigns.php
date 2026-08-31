<?php

namespace App\Filament\Resources\AgencyEmailCampaignResource\Pages;

use App\Filament\Resources\AgencyEmailCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgencyEmailCampaigns extends ListRecords
{
    protected static string $resource = AgencyEmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
