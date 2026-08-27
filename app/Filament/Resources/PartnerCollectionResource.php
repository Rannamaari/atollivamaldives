<?php

namespace App\Filament\Resources;

use App\Enums\PartnerCollectionScope;
use App\Filament\Resources\PartnerCollectionResource\Pages;
use App\Models\PartnerCollection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PartnerCollectionResource extends Resource
{
    protected static ?string $model = PartnerCollection::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Folders / Lists';

    protected static ?string $modelLabel = 'folder / list';

    protected static ?string $pluralModelLabel = 'Folders / lists';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Folder / list details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\Select::make('scope')
                        ->options(PartnerCollectionScope::options())
                        ->default(PartnerCollectionScope::Both->value)
                        ->required()
                        ->native(false)
                        ->helperText('Choose whether this folder is for suppliers, agency partners, or both.'),
                    Forms\Components\TextInput::make('color')
                        ->placeholder('Optional, e.g. teal or #0f766e'),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull()
                        ->placeholder('Examples: Halal travel agencies, priority resorts, guest houses to contact, stopover hotels...'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('scope')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? PartnerCollectionScope::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('suppliers_count')
                    ->counts('suppliers')
                    ->label('Suppliers'),
                Tables\Columns\TextColumn::make('agency_partners_count')
                    ->counts('agencyPartners')
                    ->label('Agency partners'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('scope')->options(PartnerCollectionScope::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerCollections::route('/'),
            'create' => Pages\CreatePartnerCollection::route('/create'),
            'edit' => Pages\EditPartnerCollection::route('/{record}/edit'),
        ];
    }
}
