<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Enums\EmailTemplateType;
use App\Filament\Resources\CommunicationResource;
use App\Filament\Resources\RateRequestResource;
use App\Filament\Resources\SupplierResource;
use App\Models\SupplierContact;
use App\Services\OperationsHub\CommunicationDraftFactory;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createRateRequest')
                ->label('New Rate Request')
                ->icon('heroicon-o-envelope')
                ->modalHeading('Create rate request')
                ->form([
                    Forms\Components\Select::make('supplier_contact_id')
                        ->label('Supplier contact')
                        ->options($this->record->contacts()->orderByDesc('is_primary')->orderBy('full_name')->pluck('full_name', 'id'))
                        ->searchable(),
                    Forms\Components\TextInput::make('request_title')
                        ->default('Rates request for '.($this->record->trading_name ?: $this->record->legal_name))
                        ->required(),
                    Forms\Components\TextInput::make('requested_rate_period')
                        ->placeholder('e.g. Winter 2026 / Summer 2027')
                        ->required(),
                    Forms\Components\TextInput::make('requested_markets')
                        ->placeholder('e.g. UK, Europe, Middle East'),
                    Forms\Components\Textarea::make('requested_services')
                        ->placeholder('Room categories, meal plans, transfers, child policy, offers...')
                        ->rows(4),
                    Forms\Components\Select::make('assigned_to')
                        ->relationship('assignedUser', 'name')
                        ->default($this->record->assigned_to)
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data): void {
                    $rateRequest = $this->record->rateRequests()->create([
                        'supplier_contact_id' => $data['supplier_contact_id'] ?? null,
                        'request_title' => $data['request_title'],
                        'requested_rate_period' => $data['requested_rate_period'],
                        'requested_markets' => $data['requested_markets'] ?? null,
                        'requested_services' => $data['requested_services'] ?? null,
                        'assigned_to' => $data['assigned_to'] ?? $this->record->assigned_to,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Rate request created')
                        ->body('You can now prepare the draft email and track follow-ups from the rate request page.')
                        ->send();

                    $this->redirect(RateRequestResource::getUrl('edit', ['record' => $rateRequest]));
                }),
            Action::make('prepareRateEmail')
                ->label('Generate Email')
                ->icon('heroicon-o-pencil-square')
                ->modalHeading('Generate supplier email draft')
                ->form([
                    Forms\Components\Select::make('rate_request_id')
                        ->label('Rate request')
                        ->options($this->record->rateRequests()->latest()->pluck('request_title', 'id'))
                        ->helperText('Choose a rate request when you want a full B2B rates email. Leave blank for a general supplier introduction.')
                        ->searchable(),
                    Forms\Components\TextInput::make('recipient')
                        ->label('To email')
                        ->default($this->record->contracting_email ?: $this->record->sales_email ?: $this->record->reservations_email ?: $this->record->general_email),
                    Forms\Components\Select::make('supplier_contact_id')
                        ->label('Supplier contact')
                        ->options($this->record->contacts()->orderByDesc('is_primary')->orderBy('full_name')->pluck('full_name', 'id'))
                        ->searchable(),
                    Forms\Components\Select::make('template_type')
                        ->options([
                            EmailTemplateType::SupplierIntroduction->value => EmailTemplateType::SupplierIntroduction->label(),
                            EmailTemplateType::RequestB2BRates->value => EmailTemplateType::RequestB2BRates->label(),
                            EmailTemplateType::FirstSupplierFollowUp->value => EmailTemplateType::FirstSupplierFollowUp->label(),
                            EmailTemplateType::SecondSupplierFollowUp->value => EmailTemplateType::SecondSupplierFollowUp->label(),
                            EmailTemplateType::RequestUpdatedRates->value => EmailTemplateType::RequestUpdatedRates->label(),
                        ])
                        ->default(EmailTemplateType::RequestB2BRates->value)
                        ->required()
                        ->native(false),
                    Forms\Components\TextInput::make('requested_rate_period')
                        ->label('Rate period')
                        ->placeholder('e.g. Winter 2026 / Summer 2027'),
                    Forms\Components\TextInput::make('requested_markets')
                        ->label('Markets')
                        ->placeholder('e.g. UK, Europe, Middle East'),
                    Forms\Components\Textarea::make('specific_request')
                        ->label('Specific request / notes')
                        ->rows(4)
                        ->placeholder('Any special contracting points, documents, offers, transfer details, or extra notes...'),
                ])
                ->action(function (array $data, CommunicationDraftFactory $draftFactory): void {
                    $rateRequest = filled($data['rate_request_id'] ?? null)
                        ? $this->record->rateRequests()->findOrFail($data['rate_request_id'])
                        : null;
                    $contact = filled($data['supplier_contact_id'] ?? null)
                        ? SupplierContact::find($data['supplier_contact_id'])
                        : ($rateRequest?->supplierContact ?? $this->record->contacts()->where('is_primary', true)->first());

                    $templateType = EmailTemplateType::from($data['template_type']);

                    $communication = $draftFactory->createDraft(
                        supplier: $this->record,
                        rateRequest: $rateRequest,
                        contact: $contact,
                        templateType: $templateType,
                        templateContext: [
                            'markets' => $data['requested_markets'] ?? $rateRequest?->requested_markets,
                            'specific_request' => $data['specific_request'] ?? $rateRequest?->requested_services,
                        ],
                        overrides: [
                            'recipient' => $data['recipient'] ?? $contact?->email,
                        ],
                    );

                    if ($rateRequest) {
                        $rateRequest->update([
                            'drafted_at' => $rateRequest->drafted_at ?? now(),
                            'requested_rate_period' => $data['requested_rate_period'] ?? $rateRequest->requested_rate_period,
                            'requested_markets' => $data['requested_markets'] ?? $rateRequest->requested_markets,
                            'requested_services' => $data['specific_request'] ?? $rateRequest->requested_services,
                        ]);
                    }

                    Notification::make()
                        ->success()
                        ->title('Draft email prepared')
                        ->body('Review the draft, then mark it as sent once you have emailed the supplier.')
                        ->send();

                    $this->redirect(CommunicationResource::getUrl('edit', ['record' => $communication]));
                }),
            Action::make('logCommunication')
                ->label('Log Call / WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->modalHeading('Log communication')
                ->form([
                    Forms\Components\Select::make('supplier_contact_id')
                        ->label('Supplier contact')
                        ->options($this->record->contacts()->orderByDesc('is_primary')->orderBy('full_name')->pluck('full_name', 'id'))
                        ->searchable(),
                    Forms\Components\Select::make('channel')
                        ->options([
                            CommunicationChannel::PhoneCall->value => CommunicationChannel::PhoneCall->label(),
                            CommunicationChannel::WhatsApp->value => CommunicationChannel::WhatsApp->label(),
                            CommunicationChannel::Meeting->value => CommunicationChannel::Meeting->label(),
                            CommunicationChannel::EmailReceived->value => CommunicationChannel::EmailReceived->label(),
                            CommunicationChannel::Other->value => CommunicationChannel::Other->label(),
                        ])
                        ->required(),
                    Forms\Components\Select::make('direction')
                        ->options([
                            CommunicationDirection::Outbound->value => CommunicationDirection::Outbound->label(),
                            CommunicationDirection::Inbound->value => CommunicationDirection::Inbound->label(),
                        ])
                        ->default(CommunicationDirection::Outbound->value)
                        ->required(),
                    Forms\Components\TextInput::make('subject')
                        ->placeholder('e.g. Follow-up call about 2027 rates')
                        ->required(),
                    Forms\Components\Textarea::make('body')
                        ->label('Summary')
                        ->rows(5)
                        ->required(),
                    Forms\Components\Toggle::make('follow_up_required')
                        ->default(false)
                        ->live(),
                    Forms\Components\DateTimePicker::make('next_follow_up_at')
                        ->visible(fn (callable $get) => (bool) $get('follow_up_required')),
                ])
                ->action(function (array $data): void {
                    $communication = $this->record->communications()->create([
                        'supplier_contact_id' => $data['supplier_contact_id'] ?? null,
                        'channel' => $data['channel'],
                        'direction' => $data['direction'],
                        'status' => CommunicationStatus::Completed,
                        'subject' => $data['subject'],
                        'body' => $data['body'],
                        'occurred_at' => now(),
                        'follow_up_required' => (bool) ($data['follow_up_required'] ?? false),
                        'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
                        'logged_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Communication logged')
                        ->body('The supplier timeline and follow-up queue have been updated.')
                        ->send();

                    $this->redirect(CommunicationResource::getUrl('edit', ['record' => $communication]));
                }),
        ];
    }
}
