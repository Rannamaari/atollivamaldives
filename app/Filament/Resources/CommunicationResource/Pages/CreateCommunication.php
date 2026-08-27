<?php

namespace App\Filament\Resources\CommunicationResource\Pages;

use App\Enums\CommunicationStatus;
use App\Filament\Resources\CommunicationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCommunication extends CreateRecord
{
    protected static string $resource = CommunicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['status'] ?? null) !== CommunicationStatus::Draft->value && blank($data['occurred_at'] ?? null)) {
            $data['occurred_at'] = now();
        }

        $data['logged_by'] = auth()->id();

        return $data;
    }
}
