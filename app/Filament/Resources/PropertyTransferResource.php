<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyTransferResource\Pages;
use App\Models\PropertyTransfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PropertyTransferResource extends Resource
{
    protected static ?string $model = PropertyTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Rates';

    protected static ?string $navigationLabel = 'Transfers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Transfer details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('accommodation_id')
                        ->relationship('accommodation', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Property'),
                    Forms\Components\Select::make('transfer_type')
                        ->options([
                            'speedboat' => 'Speedboat',
                            'seaplane' => 'Seaplane',
                            'domestic_flight' => 'Domestic Flight',
                            'ferry' => 'Ferry',
                            'private_transfer' => 'Private Transfer',
                            'no_transfer_required' => 'No Transfer Required',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('duration')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('adult_price')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('child_price')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('infant_price')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('currency')->default('USD')->maxLength(3),
                    Forms\Components\Toggle::make('mandatory')->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('accommodation.name')->label('Property')->searchable(),
                Tables\Columns\TextColumn::make('transfer_type')->badge(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('adult_price')->money(fn ($record) => $record->currency),
                Tables\Columns\IconColumn::make('mandatory')->boolean(),
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
            'index' => Pages\ListPropertyTransfers::route('/'),
            'create' => Pages\CreatePropertyTransfer::route('/create'),
            'edit' => Pages\EditPropertyTransfer::route('/{record}/edit'),
        ];
    }
}
