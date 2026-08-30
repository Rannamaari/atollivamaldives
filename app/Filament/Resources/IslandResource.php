<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IslandResource\Pages;
use App\Models\Island;
use App\Support\OptimizedImageUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class IslandResource extends Resource
{
    protected static ?string $model = Island::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Destinations';

    protected static ?string $navigationLabel = 'Islands';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Island details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('atoll_id')
                        ->relationship('atoll', 'name')
                        ->searchable()
                        ->preload()
                        ->label('Atoll'),
                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                            'inactive' => 'Inactive',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state)))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('latitude')
                        ->numeric(),
                    Forms\Components\TextInput::make('longitude')
                        ->numeric(),
                    OptimizedImageUpload::make(
                        Forms\Components\FileUpload::make('featured_image'),
                        'destinations/islands',
                        maxWidth: 1800,
                        maxHeight: 1200,
                        quality: 82,
                    )->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('atoll.name')->label('Atoll')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
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
            'index' => Pages\ListIslands::route('/'),
            'create' => Pages\CreateIsland::route('/create'),
            'edit' => Pages\EditIsland::route('/{record}/edit'),
        ];
    }
}
