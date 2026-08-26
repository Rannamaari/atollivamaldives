<?php

namespace App\Filament\Resources\GuestHouseResource\Pages;

use App\Filament\Resources\GuestHouseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuestHouses extends ListRecords
{
    protected static string $resource = GuestHouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
