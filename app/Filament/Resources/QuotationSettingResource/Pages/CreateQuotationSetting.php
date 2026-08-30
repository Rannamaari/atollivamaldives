<?php

namespace App\Filament\Resources\QuotationSettingResource\Pages;

use App\Filament\Resources\QuotationSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotationSetting extends CreateRecord
{
    protected static string $resource = QuotationSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
