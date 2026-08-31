<?php

namespace App\Filament\Resources\AgencyEmailCampaignResource\Pages;

use App\Enums\AgencyEmailCampaignStatus;
use App\Filament\Resources\AgencyEmailCampaignResource;
use App\Models\AgencyEmailCampaign;
use App\Services\OperationsHub\AgencyEmailCampaignService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAgencyEmailCampaign extends CreateRecord
{
    protected static string $resource = AgencyEmailCampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = AgencyEmailCampaignStatus::Draft->value;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var AgencyEmailCampaign $campaign */
        $campaign = static::getModel()::create($data);

        app(AgencyEmailCampaignService::class)->syncRecipientsAndSchedule($campaign);

        return $campaign;
    }
}
