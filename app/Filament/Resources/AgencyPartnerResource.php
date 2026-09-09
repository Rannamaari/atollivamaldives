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
use Illuminate\Support\Facades\DB;

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
            ->searchPlaceholder('Search agencies, contacts, emails, countries, or folders')
            ->searchDebounce('700ms')
            ->paginated([10, 25, 50])
            ->columns([
                Tables\Columns\TextColumn::make('agency_display_name')
                    ->label('Agency')
                    ->state(fn (AgencyPartner $record) => $record->trading_name ?: $record->legal_company_name)
                    ->searchable(query: fn ($query, string $search): mixed => static::applySearch($query, $search))
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

    /**
     * Search agency records, their contacts, and assigned folders in one place.
     */
    private static function applySearch($query, string $search): mixed
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        if (DB::connection()->getDriverName() === 'pgsql') {
            $tsQuery = static::toPrefixTsQuery($search);

            if ($tsQuery !== '') {
                return $query->where(function ($agencyQuery) use ($tsQuery): void {
                    $agencyQuery
                        ->whereRaw("to_tsvector('simple', coalesce(legal_company_name, '') || ' ' || coalesce(trading_name, '') || ' ' || coalesce(country, '') || ' ' || coalesce(city, '') || ' ' || coalesce(email, '') || ' ' || coalesce(website, '') || ' ' || coalesce(licence_number, '') || ' ' || coalesce(target_customer_segment, '') || ' ' || coalesce(source_markets, '') || ' ' || coalesce(preferred_products, '')) @@ to_tsquery('simple', ?)", [$tsQuery])
                        ->orWhereHas('contacts', fn ($contactQuery) => $contactQuery->whereRaw("to_tsvector('simple', coalesce(full_name, '') || ' ' || coalesce(position, '') || ' ' || coalesce(department, '') || ' ' || coalesce(email, '') || ' ' || coalesce(telephone, '') || ' ' || coalesce(whatsapp_number, '')) @@ to_tsquery('simple', ?)", [$tsQuery]))
                        ->orWhereHas('collections', fn ($collectionQuery) => $collectionQuery->whereRaw("to_tsvector('simple', name) @@ to_tsquery('simple', ?)", [$tsQuery]));
                });
            }
        }

        $pattern = '%'.mb_strtolower($search).'%';

        return $query->where(function ($agencyQuery) use ($pattern): void {
            foreach (['legal_company_name', 'trading_name', 'country', 'city', 'email', 'website', 'licence_number', 'target_customer_segment', 'source_markets', 'preferred_products'] as $column) {
                $agencyQuery->orWhereRaw("LOWER(COALESCE({$column}, '')) LIKE ?", [$pattern]);
            }

            $agencyQuery
                ->orWhereHas('contacts', function ($contactQuery) use ($pattern): void {
                    foreach (['full_name', 'position', 'department', 'email', 'telephone', 'whatsapp_number'] as $column) {
                        $contactQuery->orWhereRaw("LOWER(COALESCE({$column}, '')) LIKE ?", [$pattern]);
                    }
                })
                ->orWhereHas('collections', fn ($collectionQuery) => $collectionQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]));
        });
    }

    private static function toPrefixTsQuery(string $search): string
    {
        $terms = preg_split('/[^[:alnum:]]+/u', mb_strtolower($search), -1, PREG_SPLIT_NO_EMPTY);

        return collect($terms)
            ->filter(fn (string $term): bool => mb_strlen($term) >= 2)
            ->map(fn (string $term): string => str_replace("'", "''", $term).':*')
            ->implode(' & ');
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
