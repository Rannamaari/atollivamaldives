<?php

namespace App\Filament\Resources\AccommodationResource\Pages;

use App\Filament\Resources\AccommodationResource;
use App\Filament\Resources\Pages\EditTravelProduct;
use Filament\Actions;

class EditAccommodation extends EditTravelProduct
{
    protected static string $resource = AccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
