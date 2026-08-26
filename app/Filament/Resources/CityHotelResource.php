<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
use App\Filament\Resources\CityHotelResource\Pages;

class CityHotelResource extends AbstractTravelProductResource
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'City Hotels';

    protected static ?string $modelLabel = 'city hotel';

    protected static ?string $pluralModelLabel = 'city hotels';

    protected static ?int $navigationSort = 3;

    protected static function getTravelProductType(): AccommodationType
    {
        return AccommodationType::CityHotel;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCityHotels::route('/'),
            'create' => Pages\CreateCityHotel::route('/create'),
            'edit' => Pages\EditCityHotel::route('/{record}/edit'),
        ];
    }
}
