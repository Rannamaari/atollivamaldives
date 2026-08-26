<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Properties';

    protected static ?string $navigationLabel = 'Rooms';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Room details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('accommodation_id')
                        ->relationship('accommodation', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Property'),
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
                    Forms\Components\TextInput::make('short_description')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('room_size'),
                    Forms\Components\TextInput::make('bed_type'),
                    Forms\Components\TextInput::make('max_adults')->numeric(),
                    Forms\Components\TextInput::make('max_children')->numeric(),
                    Forms\Components\TextInput::make('max_occupancy')->numeric(),
                ]),
            Forms\Components\Section::make('Pricing & media')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('base_price')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('currency')->default('USD')->maxLength(3),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    Forms\Components\Toggle::make('featured')->default(false),
                    Forms\Components\Select::make('facilities')
                        ->relationship('facilities', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),
                    Forms\Components\FileUpload::make('room_images_upload')
                        ->label('Room gallery')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('rooms')
                        ->saveRelationshipsUsing(function (Room $record, ?array $state): void {
                            if (! $state) {
                                return;
                            }

                            $record->images()->delete();

                            foreach (array_values($state) as $index => $imagePath) {
                                $record->images()->create([
                                    'image_path' => $imagePath,
                                    'sort_order' => $index,
                                    'is_featured' => $index === 0,
                                ]);
                            }
                        })
                        ->dehydrated(false)
                        ->afterStateHydrated(function (Forms\Components\FileUpload $component, ?Room $record): void {
                            if (! $record) {
                                return;
                            }

                            $component->state($record->images->pluck('image_path')->all());
                        })
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('accommodation.name')->label('Property')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('base_price')->money(fn ($record) => $record->currency),
                Tables\Columns\TextColumn::make('max_occupancy')->label('Guests'),
                Tables\Columns\IconColumn::make('featured')->boolean(),
                Tables\Columns\TextColumn::make('status')->badge(),
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
