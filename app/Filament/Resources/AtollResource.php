<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtollResource\Pages;
use App\Models\Atoll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AtollResource extends Resource
{
    protected static ?string $model = Atoll::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Destinations';

    protected static ?string $navigationLabel = 'Atolls';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Atoll details')
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
                    Forms\Components\TextInput::make('code')
                        ->maxLength(50),
                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                            'inactive' => 'Inactive',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\FileUpload::make('featured_image')
                        ->image()
                        ->disk('public')
                        ->directory('destinations/atolls')
                        ->imageEditor()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')->label('Image'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('islands_count')->counts('islands')->label('Islands'),
                Tables\Columns\TextColumn::make('accommodations_count')->counts('accommodations')->label('Properties'),
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
            'index' => Pages\ListAtolls::route('/'),
            'create' => Pages\CreateAtoll::route('/create'),
            'edit' => Pages\EditAtoll::route('/{record}/edit'),
        ];
    }
}
