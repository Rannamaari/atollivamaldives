<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Concerns\HandlesLegacyRemoteImages;
use App\Filament\Resources\SiteSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditSiteSetting extends EditRecord
{
    use HandlesLegacyRemoteImages;

    protected static string $resource = SiteSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->nullifyLegacyRemoteImageFields($data, [
            'hero_image',
            'default_og_image',
            'business_logo',
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->restoreLegacyRemoteImageFields($data, $this->getRecord(), [
            'hero_image',
            'default_og_image',
            'business_logo',
        ]);
    }
}
