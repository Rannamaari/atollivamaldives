<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
use App\Filament\Resources\PackageResource\Pages;

class PackageResource extends AbstractTravelProductResource
{
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Packages';

    protected static ?string $modelLabel = 'package';

    protected static ?string $pluralModelLabel = 'packages';

    protected static ?int $navigationSort = 4;

    protected static function getTravelProductType(): AccommodationType
    {
        return AccommodationType::Package;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
