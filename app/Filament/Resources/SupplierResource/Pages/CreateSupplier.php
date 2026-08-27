<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Enums\EmailTemplateType;
use App\Filament\Resources\CommunicationResource;
use App\Filament\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\OperationsHub\CommunicationDraftFactory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;

    protected ?int $draftCommunicationId = null;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var Supplier $supplier */
        $supplier = static::getModel()::create($data);

        $contact = $supplier->contacts()->where('is_primary', true)->first()
            ?: $supplier->contacts()->where('is_active', true)->first();

        $draft = app(CommunicationDraftFactory::class)->createDraft(
            supplier: $supplier,
            contact: $contact,
            templateType: EmailTemplateType::SupplierIntroduction,
        );

        $this->draftCommunicationId = $draft->id;

        return $supplier;
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
            ->title('Supplier created')
            ->body('A ready-to-copy introduction email draft has been prepared. Send it manually, then use "Mark sent" to start the follow-up reminders.');
    }
}
