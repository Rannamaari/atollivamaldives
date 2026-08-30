<?php

namespace App\Filament\Resources\HomePageResource\Pages;

use App\Filament\Concerns\HandlesLegacyRemoteImages;
use App\Filament\Resources\HomePageResource;
use Filament\Resources\Pages\EditRecord;

class EditHomePage extends EditRecord
{
    use HandlesLegacyRemoteImages;

    protected static string $resource = HomePageResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->nullifyLegacyRemoteImageFields($data, [
            'hero_image',
            'resorts_card_image',
            'guesthouses_card_image',
            'city_hotels_card_image',
            'liveaboards_card_image',
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->restoreLegacyRemoteImageFields($data, $this->getRecord(), [
            'hero_image',
            'resorts_card_image',
            'guesthouses_card_image',
            'city_hotels_card_image',
            'liveaboards_card_image',
        ]);
    }
}
