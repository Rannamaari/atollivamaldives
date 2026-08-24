<?php

namespace App\Filament\Resources\BlogOfferResource\Pages;

use App\Filament\Resources\BlogOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogOffer extends EditRecord
{
    protected static string $resource = BlogOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
