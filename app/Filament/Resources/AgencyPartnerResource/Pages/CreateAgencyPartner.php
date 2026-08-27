<?php

namespace App\Filament\Resources\AgencyPartnerResource\Pages;

use App\Enums\EmailTemplateType;
use App\Filament\Resources\AgencyPartnerResource;
use App\Filament\Resources\CommunicationResource;
use App\Models\AgencyPartner;
use App\Services\OperationsHub\CommunicationDraftFactory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAgencyPartner extends CreateRecord
{
    protected static string $resource = AgencyPartnerResource::class;

    protected ?int $draftCommunicationId = null;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var AgencyPartner $agencyPartner */
        $agencyPartner = static::getModel()::create($data);

        $contact = $agencyPartner->contacts()->where('is_primary', true)->first()
            ?: $agencyPartner->contacts()->where('is_active', true)->first();

        $draft = app(CommunicationDraftFactory::class)->createDraft(
            agencyPartner: $agencyPartner,
            contact: $contact,
            templateType: EmailTemplateType::AgencyIntroduction,
        );

        $this->draftCommunicationId = $draft->id;

        return $agencyPartner;
    }

    protected function getRedirectUrl(): string
    {
        if ($this->draftCommunicationId) {
            return CommunicationResource::getUrl('edit', ['record' => $this->draftCommunicationId]);
        }

        return parent::getRedirectUrl();
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Agency partner created')
            ->body('A ready-to-copy introduction email draft has been prepared. Send it manually, then use "Mark sent" to activate the follow-up reminders.');
    }
}
