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
            ->columns([
                Tables\Columns\TextColumn::make('supplier_display_name')
                    ->label('Supplier')
                    ->state(fn (Supplier $record) => $record->trading_name ?: $record->legal_name)
                    ->searchable(query: function ($query, string $search): void {
                        $query
                            ->where(function ($supplierQuery) use ($search): void {
                                $supplierQuery
                                    ->where('trading_name', 'like', "%{$search}%")
                                    ->orWhere('legal_name', 'like', "%{$search}%")
                                    ->orWhere('general_email', 'like', "%{$search}%")
                                    ->orWhere('sales_email', 'like', "%{$search}%")
                                    ->orWhere('reservations_email', 'like', "%{$search}%")
                                    ->orWhere('contracting_email', 'like', "%{$search}%")
                                    ->orWhere('main_telephone', 'like', "%{$search}%")
                                    ->orWhere('website', 'like', "%{$search}%")
                                    ->orWhere('country', 'like', "%{$search}%")
                                    ->orWhereHas('contacts', function ($contactQuery) use ($search): void {
                                        $contactQuery
                                            ->where('full_name', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%")
                                            ->orWhere('telephone', 'like', "%{$search}%");
                                    });
                            });
                    })
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
