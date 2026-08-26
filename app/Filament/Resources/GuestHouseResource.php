<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
use App\Filament\Resources\GuestHouseResource\Pages;

class GuestHouseResource extends AbstractTravelProductResource
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Guest Houses';

    protected static ?string $modelLabel = 'guest house';

    protected static ?string $pluralModelLabel = 'guest houses';

    protected static ?int $navigationSort = 2;

    protected static function getTravelProductType(): AccommodationType
    {
        return AccommodationType::Guesthouse;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuestHouses::route('/'),
            'create' => Pages\CreateGuestHouse::route('/create'),
            'edit' => Pages\EditGuestHouse::route('/{record}/edit'),
        ];
    }
}
