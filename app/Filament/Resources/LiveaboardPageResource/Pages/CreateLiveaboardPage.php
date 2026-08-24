<?php

namespace App\Filament\Resources\LiveaboardPageResource\Pages;

use App\Filament\Resources\LiveaboardPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLiveaboardPage extends CreateRecord
{
    protected static string $resource = LiveaboardPageResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
