<?php

namespace App\Filament\Resources\BlogOfferResource\Pages;

use App\Filament\Concerns\HandlesLegacyRemoteImages;
use App\Filament\Resources\BlogOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogOffer extends EditRecord
{
    use HandlesLegacyRemoteImages;

    protected static string $resource = BlogOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->nullifyLegacyRemoteImageFields($data, ['image']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->restoreLegacyRemoteImageFields($data, $this->getRecord(), ['image']);
    }
}
