<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Enums\EmailTemplateType;
use App\Enums\RateRequestStatus;
use App\Filament\Resources\CommunicationResource;
use App\Models\SupplierContact;
use App\Services\OperationsHub\CommunicationDraftFactory;
use App\Services\OperationsHub\RateRequestWorkflow;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class RateRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'rateRequests';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('supplier_contact_id')->relationship('supplierContact', 'full_name')->searchable()->preload(),
            Forms\Components\TextInput::make('request_title')->required(),
            Forms\Components\TextInput::make('requested_rate_period'),
            Forms\Components\TextInput::make('requested_markets'),
            Forms\Components\Textarea::make('requested_services')->columnSpanFull(),
            Forms\Components\Select::make('status')->options(RateRequestStatus::options())->default(RateRequestStatus::Draft->value)->required(),
            Forms\Components\DateTimePicker::make('next_follow_up_at'),
            Forms\Components\Select::make('assigned_to')->relationship('assignedUser', 'name')->searchable()->preload(),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_title')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? RateRequestStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('sent_at')->dateTime(),
                Tables\Columns\TextColumn::make('next_follow_up_at')->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('prepare_email')
                    ->label('Prepare email')
                    ->form([
                        Forms\Components\Select::make('supplier_contact_id')
                            ->label('Supplier contact')
                            ->options(fn ($record) => $record->supplier?->contacts()->orderByDesc('is_primary')->orderBy('full_name')->pluck('full_name', 'id') ?? [])
                            ->searchable(),
                        Forms\Components\Select::make('template_type')
                            ->options([
                                EmailTemplateType::RequestB2BRates->value => EmailTemplateType::RequestB2BRates->label(),
                                EmailTemplateType::FirstSupplierFollowUp->value => EmailTemplateType::FirstSupplierFollowUp->label(),
                                EmailTemplateType::SecondSupplierFollowUp->value => EmailTemplateType::SecondSupplierFollowUp->label(),
                            ])
                            ->default(EmailTemplateType::RequestB2BRates->value)
                            ->required(),
                    ])
                    ->action(function ($record, array $data, CommunicationDraftFactory $draftFactory): void {
                        $contact = filled($data['supplier_contact_id'] ?? null)
                            ? SupplierContact::find($data['supplier_contact_id'])
                            : ($record->supplierContact ?? $record->supplier?->contacts()->where('is_primary', true)->first());

                        $communication = $draftFactory->createDraft(
                            supplier: $record->supplier,
                            rateRequest: $record,
                            contact: $contact,
                            templateType: EmailTemplateType::from($data['template_type']),
                        );

                        $record->update([
                            'drafted_at' => $record->drafted_at ?? now(),
                        ]);

                        Notification::make()->success()->title('Draft created')->send();

                        $this->redirect(CommunicationResource::getUrl('edit', ['record' => $communication]));
                    }),
                Action::make('mark_sent')
                    ->requiresConfirmation()
                    ->action(fn ($record) => app(RateRequestWorkflow::class)->markSent($record)),
            ]);
    }
}
