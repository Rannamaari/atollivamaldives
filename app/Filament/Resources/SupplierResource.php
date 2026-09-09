<?php

namespace App\Filament\Resources;

use App\Enums\SupplierPartnershipStatus;
use App\Enums\SupplierType;
use App\Filament\RelationManagers\OperationsHub\ActivityEventsRelationManager;
use App\Filament\RelationManagers\OperationsHub\CommunicationsRelationManager;
use App\Filament\RelationManagers\OperationsHub\DocumentsRelationManager;
use App\Filament\RelationManagers\OperationsHub\InternalNotesRelationManager;
use App\Filament\RelationManagers\OperationsHub\OperationsTasksRelationManager;
use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers\RateRequestsRelationManager;
use App\Filament\Resources\SupplierResource\RelationManagers\SupplierContactsRelationManager;
use App\Models\PartnerCollection;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Suppliers';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Essential details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('legal_name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('trading_name')->maxLength(255),
                    Forms\Components\Select::make('supplier_type')
                        ->options(SupplierType::options())
                        ->required()
                        ->default(SupplierType::Resort->value)
                        ->native(false),
                    Forms\Components\Select::make('partnership_status')
                        ->options(SupplierPartnershipStatus::options())
                        ->required()
                        ->default(SupplierPartnershipStatus::NotContacted->value)
                        ->native(false)
                        ->helperText('Start with "Not Contacted" unless you have already reached out.'),
                    Forms\Components\TextInput::make('atoll'),
                    Forms\Components\TextInput::make('island'),
                    Forms\Components\TextInput::make('country')->default('Maldives')->required(),
                    Forms\Components\TextInput::make('website')->url(),
                ]),
            Forms\Components\Section::make('Main contact channels')
                ->description('Only fill the main email or phone details you actually have right now. The rest can be added later.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('general_email')->email(),
                    Forms\Components\TextInput::make('sales_email')->email(),
                    Forms\Components\TextInput::make('reservations_email')->email(),
                    Forms\Components\TextInput::make('contracting_email')->email(),
                    Forms\Components\TextInput::make('main_telephone'),
                    Forms\Components\TextInput::make('whatsapp_number'),
                ]),
            Forms\Components\Section::make('Internal notes')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('assigned_to')->relationship('assignedUser', 'name')->searchable()->preload(),
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\Select::make('collections')
                        ->label('Folders / lists')
                        ->relationship(
                            name: 'collections',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->whereIn('scope', ['suppliers', 'both'])->where('is_active', true)
                        )
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->native(false)
                        ->helperText('Use collections like "Priority Resorts" or "Guest Houses to Contact".'),
                    Forms\Components\Textarea::make('internal_notes')->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Advanced tracking and contracting')
                ->description('These are mainly for later follow-up, rates, and contracts. You do not need them when first adding a supplier.')
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('registration_number'),
                    Forms\Components\TextInput::make('accounts_email')->email(),
                    Forms\Components\DatePicker::make('first_contacted_at'),
                    Forms\Components\DateTimePicker::make('last_contacted_at'),
                    Forms\Components\DateTimePicker::make('next_follow_up_at'),
                    Forms\Components\DatePicker::make('agreement_start_date'),
                    Forms\Components\DatePicker::make('agreement_expiry_date'),
                    Forms\Components\DatePicker::make('rate_validity_start_date'),
                    Forms\Components\DatePicker::make('rate_validity_end_date'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('legal_name')
            ->searchPlaceholder('Search suppliers, contacts, emails, locations, or folders')
            ->searchDebounce('700ms')
            ->paginated([10, 25, 50])
            ->columns([
                Tables\Columns\TextColumn::make('supplier_display_name')
                    ->label('Supplier')
                    ->state(fn (Supplier $record) => $record->trading_name ?: $record->legal_name)
                    ->searchable(query: fn ($query, string $search): mixed => static::applySearch($query, $search))
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw("coalesce(nullif(trading_name, ''), legal_name) {$direction}")),
                Tables\Columns\TextColumn::make('legal_name')->toggleable(),
                Tables\Columns\TextColumn::make('supplier_type')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? SupplierType::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('collections.name')
                    ->label('Folders')
                    ->badge()
                    ->separator(', ')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('partnership_status')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? SupplierPartnershipStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('assignedUser.name')->label('Assigned'),
                Tables\Columns\TextColumn::make('next_follow_up_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('rate_validity_end_date')->date()->sortable()->label('Rate expiry'),
                Tables\Columns\TextColumn::make('agreement_expiry_date')->date()->sortable()->label('Agreement expiry'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier_type')->options(SupplierType::options()),
                Tables\Filters\SelectFilter::make('collection')
                    ->label('Folder / list')
                    ->options(fn () => PartnerCollection::query()->whereIn('scope', ['suppliers', 'both'])->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function ($query, array $data): void {
                        if (blank($data['value'] ?? null)) {
                            return;
                        }

                        $query->whereHas('collections', fn ($collectionQuery) => $collectionQuery->whereKey($data['value']));
                    }),
                Tables\Filters\SelectFilter::make('partnership_status')->options(SupplierPartnershipStatus::options()),
                Tables\Filters\SelectFilter::make('assigned_to')->relationship('assignedUser', 'name'),
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\Filter::make('follow_up_due')->query(fn ($query) => $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now())),
                Tables\Filters\Filter::make('rate_expiry_30')->query(fn ($query) => $query->whereBetween('rate_validity_end_date', [today(), today()->addDays(30)])),
                Tables\Filters\Filter::make('agreement_expiry_60')->query(fn ($query) => $query->whereBetween('agreement_expiry_date', [today(), today()->addDays(60)])),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('generate_email')
                        ->label('Generate Email')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn (Supplier $record) => Pages\EditSupplier::getUrl(['record' => $record]).'#generate-email'),
                    Tables\Actions\Action::make('log_communication')
                        ->label('Add Communication')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn (Supplier $record) => Pages\EditSupplier::getUrl(['record' => $record]).'#log-communication'),
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
                                ->options(fn () => PartnerCollection::query()->whereIn('scope', ['suppliers', 'both'])->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
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
                                ->options(fn () => PartnerCollection::query()->whereIn('scope', ['suppliers', 'both'])->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                                ->required()
                                ->searchable()
                                ->native(false),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                $record->collections()->detach($data['partner_collection_id']);
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ]);
    }

    /**
     * Keep the search broad enough for operations work while using the indexed
     * PostgreSQL document search in production.
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
                return $query->where(function ($supplierQuery) use ($tsQuery): void {
                    $supplierQuery
                        ->whereRaw("to_tsvector('simple', coalesce(legal_name, '') || ' ' || coalesce(trading_name, '') || ' ' || coalesce(atoll, '') || ' ' || coalesce(island, '') || ' ' || coalesce(country, '') || ' ' || coalesce(general_email, '') || ' ' || coalesce(sales_email, '') || ' ' || coalesce(reservations_email, '') || ' ' || coalesce(contracting_email, '') || ' ' || coalesce(accounts_email, '') || ' ' || coalesce(main_telephone, '') || ' ' || coalesce(whatsapp_number, '') || ' ' || coalesce(website, '')) @@ to_tsquery('simple', ?)", [$tsQuery])
                        ->orWhereHas('contacts', fn ($contactQuery) => $contactQuery->whereRaw("to_tsvector('simple', coalesce(full_name, '') || ' ' || coalesce(job_title, '') || ' ' || coalesce(department, '') || ' ' || coalesce(email, '') || ' ' || coalesce(telephone, '') || ' ' || coalesce(whatsapp_number, '')) @@ to_tsquery('simple', ?)", [$tsQuery]))
                        ->orWhereHas('collections', fn ($collectionQuery) => $collectionQuery->whereRaw("to_tsvector('simple', name) @@ to_tsquery('simple', ?)", [$tsQuery]));
                });
            }
        }

        $pattern = '%'.mb_strtolower($search).'%';

        return $query->where(function ($supplierQuery) use ($pattern): void {
            foreach (['legal_name', 'trading_name', 'atoll', 'island', 'country', 'general_email', 'sales_email', 'reservations_email', 'contracting_email', 'accounts_email', 'main_telephone', 'whatsapp_number', 'website'] as $column) {
                $supplierQuery->orWhereRaw("LOWER(COALESCE({$column}, '')) LIKE ?", [$pattern]);
            }

            $supplierQuery
                ->orWhereHas('contacts', function ($contactQuery) use ($pattern): void {
                    foreach (['full_name', 'job_title', 'department', 'email', 'telephone', 'whatsapp_number'] as $column) {
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
            SupplierContactsRelationManager::class,
            RateRequestsRelationManager::class,
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
