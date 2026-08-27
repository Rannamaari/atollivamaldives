<?php

namespace App\Filament\Resources\AgencyPartnerResource\Pages;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Enums\EmailTemplateType;
use App\Filament\Resources\AgencyPartnerResource;
use App\Filament\Resources\CommunicationResource;
use App\Models\AgencyContact;
use App\Services\OperationsHub\CommunicationDraftFactory;
use App\Services\OperationsHub\FollowUpScheduler;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAgencyPartner extends EditRecord
{
    protected static string $resource = AgencyPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('prepareIntroduction')
                ->label('Generate Email')
                ->icon('heroicon-o-pencil-square')
                ->modalHeading('Generate agency email draft')
                ->form([
                    Forms\Components\TextInput::make('recipient')
                        ->label('To email')
                        ->default(fn () => $this->record->contacts()->where('is_primary', true)->value('email')),
                    Forms\Components\Select::make('agency_contact_id')
                        ->label('Agency contact')
                        ->options($this->record->contacts()->orderByDesc('is_primary')->orderBy('full_name')->pluck('full_name', 'id'))
                        ->searchable(),
                    Forms\Components\Select::make('template_type')
                        ->options([
                            EmailTemplateType::AgencyIntroduction->value => EmailTemplateType::AgencyIntroduction->label(),
                            EmailTemplateType::AgencyPartnershipInvitation->value => EmailTemplateType::AgencyPartnershipInvitation->label(),
                            EmailTemplateType::FirstAgencyFollowUp->value => EmailTemplateType::FirstAgencyFollowUp->label(),
                            EmailTemplateType::SecondAgencyFollowUp->value => EmailTemplateType::SecondAgencyFollowUp->label(),
                        ])
                        ->default(EmailTemplateType::AgencyIntroduction->value)
                        ->required()
                        ->native(false),
                    Forms\Components\Textarea::make('personalized_intro')
                        ->label('Personalized introduction')
                        ->rows(3)
                        ->placeholder('Optional note tailored to this agency, their market, clientele, or destination focus.'),
                    Forms\Components\Textarea::make('partnership_request')
                        ->label('Partnership request')
                        ->rows(3)
                        ->placeholder('Optional next-step ask, such as registration, contracting, a call, or a company profile exchange.'),
                ])
                ->action(function (array $data, CommunicationDraftFactory $draftFactory): void {
                    $contact = filled($data['agency_contact_id'] ?? null)
                        ? AgencyContact::find($data['agency_contact_id'])
                        : $this->record->contacts()->where('is_primary', true)->first();

                    $communication = $draftFactory->createDraft(
                        agencyPartner: $this->record,
                        contact: $contact,
                        templateType: EmailTemplateType::from($data['template_type']),
                        templateContext: [
                            'personalized_intro' => $data['personalized_intro'] ?? null,
                            'partnership_request' => $data['partnership_request'] ?? null,
                        ],
                        overrides: [
                            'recipient' => $data['recipient'] ?? $contact?->email,
                        ],
                    );

                    Notification::make()
                        ->success()
                        ->title('Agency draft prepared')
                        ->body('Review the draft, then mark it as sent after emailing the partner.')
                        ->send();

                    $this->redirect(CommunicationResource::getUrl('edit', ['record' => $communication]));
                }),
            Action::make('logCommunication')
                ->label('Log Communication')
                ->icon('heroicon-o-phone')
                ->form([
                    Forms\Components\Select::make('agency_contact_id')
                        ->label('Agency contact')
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
                    Forms\Components\TextInput::make('subject')->required(),
                    Forms\Components\Textarea::make('body')->label('Summary')->rows(5)->required(),
                    Forms\Components\Toggle::make('follow_up_required')->default(false)->live(),
                    Forms\Components\DateTimePicker::make('next_follow_up_at')
                        ->visible(fn (callable $get) => (bool) $get('follow_up_required')),
                ])
                ->action(function (array $data): void {
                    $communication = $this->record->communications()->create([
                        'agency_contact_id' => $data['agency_contact_id'] ?? null,
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
                        ->title('Agency communication logged')
                        ->body('The agency timeline and follow-up queue have been updated.')
                        ->send();

                    $this->redirect(CommunicationResource::getUrl('edit', ['record' => $communication]));
                }),
            Action::make('scheduleFollowUp')
                ->label('Create Follow-Up Task')
                ->icon('heroicon-o-clock')
                ->requiresConfirmation()
                ->action(function (FollowUpScheduler $followUpScheduler): void {
                    $task = $followUpScheduler->scheduleAgencyIntroductionFollowUp($this->record);

                    Notification::make()
                        ->success()
                        ->title('Follow-up task created')
                        ->body('Task: '.$task->title)
                        ->send();
                }),
        ];
    }
}
