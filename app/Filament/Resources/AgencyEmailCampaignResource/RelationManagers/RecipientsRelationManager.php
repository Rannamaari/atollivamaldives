<?php

namespace App\Filament\Resources\AgencyEmailCampaignResource\RelationManagers;

use App\Enums\AgencyEmailRecipientStatus;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RecipientsRelationManager extends RelationManager
{
    protected static string $relationship = 'recipients';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_for')
            ->columns([
                Tables\Columns\TextColumn::make('agencyPartner.trading_name')
                    ->label('Agency')
                    ->state(fn ($record) => $record->agencyPartner?->trading_name ?: $record->agencyPartner?->legal_company_name),
                Tables\Columns\TextColumn::make('recipient_email')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? AgencyEmailRecipientStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('scheduled_for')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('sent_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('failure_reason')->limit(40),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('recipient_email')->email(),
                        Forms\Components\Select::make('status')->options(AgencyEmailRecipientStatus::options()),
                        Forms\Components\DateTimePicker::make('scheduled_for'),
                        Forms\Components\Textarea::make('failure_reason'),
                    ]),
            ])
            ->bulkActions([]);
    }
}
