<?php

namespace App\Filament\Resources\RateRequestResource\Pages;

use App\Enums\CommunicationStatus;
use App\Enums\EmailTemplateType;
use App\Filament\Resources\CommunicationResource;
use App\Filament\Resources\RateRequestResource;
use App\Models\SupplierContact;
use App\Services\OperationsHub\CommunicationDraftFactory;
use App\Services\OperationsHub\RateRequestWorkflow;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRateRequest extends EditRecord
{
    protected static string $resource = RateRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('prepareDraft')
                ->label('Prepare Email Draft')
                ->icon('heroicon-o-pencil-square')
                ->form([
                    Forms\Components\Select::make('supplier_contact_id')
                        ->label('Supplier contact')
                        ->options($this->record->supplier?->contacts()->orderByDesc('is_primary')->orderBy('full_name')->pluck('full_name', 'id') ?? [])
                        ->default($this->record->supplier_contact_id)
                        ->searchable(),
                    Forms\Components\Select::make('template_type')
                        ->options([
                            EmailTemplateType::RequestB2BRates->value => EmailTemplateType::RequestB2BRates->label(),
                            EmailTemplateType::FirstSupplierFollowUp->value => EmailTemplateType::FirstSupplierFollowUp->label(),
                            EmailTemplateType::SecondSupplierFollowUp->value => EmailTemplateType::SecondSupplierFollowUp->label(),
                            EmailTemplateType::RequestUpdatedRates->value => EmailTemplateType::RequestUpdatedRates->label(),
                            EmailTemplateType::RequestSupplierAgreement->value => EmailTemplateType::RequestSupplierAgreement->label(),
                        ])
                        ->default(EmailTemplateType::RequestB2BRates->value)
                        ->required(),
                ])
                ->action(function (array $data, CommunicationDraftFactory $draftFactory): void {
                    $contact = filled($data['supplier_contact_id'] ?? null)
                        ? SupplierContact::find($data['supplier_contact_id'])
                        : ($this->record->supplierContact ?? $this->record->supplier?->contacts()->where('is_primary', true)->first());

                    $communication = $draftFactory->createDraft(
                        supplier: $this->record->supplier,
                        rateRequest: $this->record,
                        contact: $contact,
                        templateType: EmailTemplateType::from($data['template_type']),
                    );

                    $this->record->update([
                        'drafted_at' => $this->record->drafted_at ?? now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Draft created')
                        ->body('Review the draft, then come back and mark the rate request as sent once the email has gone out.')
                        ->send();

                    $this->redirect(CommunicationResource::getUrl('edit', ['record' => $communication]));
                }),
            Action::make('markReady')
                ->label('Mark Ready')
                ->icon('heroicon-o-check-badge')
                ->action(function (RateRequestWorkflow $workflow): void {
                    $workflow->markReady($this->record);

                    Notification::make()->success()->title('Rate request marked ready')->send();
                }),
            Action::make('markSent')
                ->label('Mark Sent')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->action(function (RateRequestWorkflow $workflow): void {
                    $workflow->markSent($this->record);

                    Notification::make()
                        ->success()
                        ->title('Rate request marked sent')
                        ->body('The sent time, supplier activity, and follow-up task were recorded.')
                        ->send();
                }),
            Action::make('recordResponse')
                ->label('Record Response')
                ->icon('heroicon-o-inbox-arrow-down')
                ->form([
                    Forms\Components\Toggle::make('rates_received')->default(false),
                    Forms\Components\Toggle::make('agreement_received')->default(false),
                    Forms\Components\Textarea::make('communication_summary')
                        ->label('What did they send or say?')
                        ->rows(4),
                ])
                ->action(function (array $data, RateRequestWorkflow $workflow): void {
                    $workflow->recordResponse(
                        $this->record,
                        (bool) ($data['rates_received'] ?? false),
                        (bool) ($data['agreement_received'] ?? false),
                    );

                    if (filled($data['communication_summary'] ?? null)) {
                        $this->record->communications()->create([
                            'supplier_id' => $this->record->supplier_id,
                            'supplier_contact_id' => $this->record->supplier_contact_id,
                            'direction' => 'inbound',
                            'channel' => 'email_received',
                            'status' => CommunicationStatus::Received,
                            'subject' => 'Response received for '.$this->record->request_title,
                            'body' => $data['communication_summary'],
                            'occurred_at' => now(),
                            'logged_by' => auth()->id(),
                        ]);
                    }

                    Notification::make()
                        ->success()
                        ->title('Response recorded')
                        ->body('The rate request and supplier status were updated.')
                        ->send();
                }),
        ];
    }
}
