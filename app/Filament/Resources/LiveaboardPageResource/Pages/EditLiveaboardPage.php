<?php

namespace App\Filament\Resources\LiveaboardPageResource\Pages;

use App\Filament\Concerns\HandlesLegacyRemoteImages;
use App\Filament\Resources\LiveaboardPageResource;
use Filament\Resources\Pages\EditRecord;

class EditLiveaboardPage extends EditRecord
{
    use HandlesLegacyRemoteImages;

    protected static string $resource = LiveaboardPageResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->nullifyLegacyRemoteImageFields($data, ['hero_image']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->restoreLegacyRemoteImageFields($data, $this->getRecord(), ['hero_image']);
    }
}
