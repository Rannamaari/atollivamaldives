<?php

namespace App\Filament\Resources\CityHotelResource\Pages;

use App\Filament\Resources\CityHotelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCityHotels extends ListRecords
{
    protected static string $resource = CityHotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
