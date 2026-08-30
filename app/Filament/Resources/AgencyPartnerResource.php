<?php

namespace App\Filament\Resources;

use App\Enums\AgencyPartnershipStatus;
use App\Enums\AgencyRiskLevel;
use App\Filament\RelationManagers\OperationsHub\ActivityEventsRelationManager;
use App\Filament\RelationManagers\OperationsHub\CommunicationsRelationManager;
use App\Filament\RelationManagers\OperationsHub\DocumentsRelationManager;
use App\Filament\RelationManagers\OperationsHub\InternalNotesRelationManager;
use App\Filament\RelationManagers\OperationsHub\OperationsTasksRelationManager;
use App\Filament\Resources\AgencyPartnerResource\Pages;
use App\Filament\Resources\AgencyPartnerResource\RelationManagers\AgencyContactsRelationManager;
use App\Models\AgencyPartner;
use App\Models\PartnerCollection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class AgencyPartnerResource extends Resource
{
    protected static ?string $model = AgencyPartner::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Agency Partners';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Agency details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('legal_company_name')->required(),
                    Forms\Components\TextInput::make('trading_name'),
                    Forms\Components\TextInput::make('country')->default('Maldives')->required(),
                    Forms\Components\TextInput::make('city'),
                    Forms\Components\TextInput::make('website')->url(),
                    Forms\Components\TextInput::make('email')->email()->label('Main email'),
                    Forms\Components\TextInput::make('licence_number'),
                    Forms\Components\TextInput::make('target_customer_segment'),
                    Forms\Components\Textarea::make('source_markets'),
                    Forms\Components\TextInput::make('estimated_booking_volume'),
                    Forms\Components\Textarea::make('preferred_products'),
                    Forms\Components\TextInput::make('preferred_currency')->maxLength(3),
                    Forms\Components\Textarea::make('commercial_arrangement'),
                    Forms\Components\Textarea::make('payment_terms'),
                    Forms\Components\TextInput::make('agreement_status'),
                    Forms\Components\Select::make('partnership_status')
                        ->options(AgencyPartnershipStatus::options())
                        ->required()
                        ->default(AgencyPartnershipStatus::ProspectIdentified->value)
                        ->native(false)
                        ->helperText('Use "Prospect Identified" for new agencies you are planning to approach.'),
                    Forms\Components\DatePicker::make('first_contacted_at'),
                    Forms\Components\DateTimePicker::make('last_contacted_at'),
                    Forms\Components\DateTimePicker::make('next_follow_up_at'),
                    Forms\Components\Select::make('assigned_to')->relationship('assignedUser', 'name')->searchable()->preload(),
                    Forms\Components\Select::make('collections')
                        ->label('Folders / lists')
                        ->relationship(
                            name: 'collections',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->whereIn('scope', ['agency_partners', 'both'])->where('is_active', true)
                        )
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->native(false)
                        ->helperText('Use collections like "Halal Travel Agencies" or "Priority Partners".'),
                    Forms\Components\Select::make('risk_level')
                        ->options(AgencyRiskLevel::options())
                        ->required()
                        ->default(AgencyRiskLevel::NotAssessed->value)
                        ->native(false),
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\Textarea::make('internal_notes')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('legal_company_name')
            ->columns([
                Tables\Columns\TextColumn::make('agency_display_name')
                    ->label('Agency')
                    ->state(fn (AgencyPartner $record) => $record->trading_name ?: $record->legal_company_name)
                    ->searchable(query: function ($query, string $search): void {
                        $query
                            ->where(function ($agencyQuery) use ($search): void {
                                $agencyQuery
                                    ->where('trading_name', 'like', "%{$search}%")
                                    ->orWhere('legal_company_name', 'like', "%{$search}%")
                                    ->orWhere('country', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('website', 'like', "%{$search}%")
                                    ->orWhereHas('contacts', function ($contactQuery) use ($search): void {
                                        $contactQuery
                                            ->where('full_name', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%")
                                            ->orWhere('telephone', 'like', "%{$search}%");
                                    });
                            });
                    })
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw("coalesce(nullif(trading_name, ''), legal_company_name) {$direction}")),
                Tables\Columns\TextColumn::make('country')->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->toggleable(),
                Tables\Columns\TextColumn::make('collections.name')
                    ->label('Folders')
                    ->badge()
                    ->separator(', ')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('partnership_status')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? AgencyPartnershipStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('risk_level')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? AgencyRiskLevel::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('assignedUser.name')->label('Assigned'),
                Tables\Columns\TextColumn::make('next_follow_up_at')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('collection')
                    ->label('Folder / list')
                    ->options(fn () => PartnerCollection::query()->whereIn('scope', ['agency_partners', 'both'])->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function ($query, array $data): void {
                        if (blank($data['value'] ?? null)) {
                            return;
                        }

                        $query->whereHas('collections', fn ($collectionQuery) => $collectionQuery->whereKey($data['value']));
                    }),
                Tables\Filters\SelectFilter::make('partnership_status')->options(AgencyPartnershipStatus::options()),
                Tables\Filters\SelectFilter::make('risk_level')->options(AgencyRiskLevel::options()),
                Tables\Filters\SelectFilter::make('assigned_to')->relationship('assignedUser', 'name'),
                Tables\Filters\Filter::make('follow_up_due')->query(fn ($query) => $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now())),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('generate_email')
                        ->label('Generate Email')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn (AgencyPartner $record) => Pages\EditAgencyPartner::getUrl(['record' => $record]).'#generate-email'),
                    Tables\Actions\Action::make('log_communication')
                        ->label('Add Communication')
                        ->icon('heroicon-o-phone')
                        ->url(fn (AgencyPartner $record) => Pages\EditAgencyPartner::getUrl(['record' => $record]).'#log-communication'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assignCollection')
                        ->label('Move to folder / list')
                        ->icon('heroicon-o-folder-plus')
                        ->form([
                            Forms\Components\Select::make('partner_collection_id')
                                ->label('Folder / list')
                                ->options(fn () => PartnerCollection::query()->whereIn('scope', ['agency_partners', 'both'])->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                                ->required()
                                ->searchable()
                                ->native(false),
                        ])
                        ->action(function ($records, array $data): void {
                            $collection = PartnerCollection::findOrFail($data['partner_collection_id']);

                            foreach ($records as $record) {
                                $record->collections()->syncWithoutDetaching([$collection->id]);
                            }
                        }),
                    Tables\Actions\BulkAction::make('removeCollection')
                        ->label('Remove from folder / list')
                        ->icon('heroicon-o-folder-minus')
                        ->form([
                            Forms\Components\Select::make('partner_collection_id')
                                ->label('Folder / list')
                                ->options(fn () => PartnerCollection::query()->whereIn('scope', ['agency_partners', 'both'])->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                                ->required()
                                ->searchable()
                                ->native(false),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                $record->collections()->detach($data['partner_collection_id']);
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AgencyContactsRelationManager::class,
            CommunicationsRelationManager::class,
            OperationsTasksRelationManager::class,
            InternalNotesRelationManager::class,
            DocumentsRelationManager::class,
            ActivityEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgencyPartners::route('/'),
            'create' => Pages\CreateAgencyPartner::route('/create'),
            'edit' => Pages\EditAgencyPartner::route('/{record}/edit'),
        ];
    }
}
