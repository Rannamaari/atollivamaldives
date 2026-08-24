<?php

namespace App\Filament\Resources\BlogOfferResource\Pages;

use App\Filament\Resources\BlogOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogOffers extends ListRecords
{
    protected static string $resource = BlogOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
