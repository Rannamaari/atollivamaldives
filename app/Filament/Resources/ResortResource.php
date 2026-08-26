<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
use App\Filament\Resources\ResortResource\Pages;

class ResortResource extends AbstractTravelProductResource
{
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Resorts';

    protected static ?string $modelLabel = 'resort';

    protected static ?string $pluralModelLabel = 'resorts';

    protected static ?int $navigationSort = 1;

    protected static function getTravelProductType(): AccommodationType
    {
        return AccommodationType::Resort;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResorts::route('/'),
            'create' => Pages\CreateResort::route('/create'),
            'edit' => Pages\EditResort::route('/{record}/edit'),
        ];
    }
}
