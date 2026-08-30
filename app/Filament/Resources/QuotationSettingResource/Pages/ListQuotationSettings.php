<?php

namespace App\Filament\Resources\QuotationSettingResource\Pages;

use App\Filament\Resources\QuotationSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuotationSettings extends ListRecords
{
    protected static string $resource = QuotationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn (): bool => static::getResource()::getModel()::query()->count() === 0),
        ];
    }
}
