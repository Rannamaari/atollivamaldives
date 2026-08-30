<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageResource\Pages;
use App\Models\HomePage;
use App\Support\OptimizedImageUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomePageResource extends Resource
{
    protected static ?string $model = HomePage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Homepage Heroes';

    protected static ?string $modelLabel = 'homepage hero';

    protected static ?string $pluralModelLabel = 'homepage heroes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Hero content')->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Internal label for identifying this homepage variant in admin.'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->helperText('Only active heroes are eligible for random display on the live homepage.'),
                Forms\Components\TextInput::make('kicker')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('heading_line_one')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('heading_line_two')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('heading_emphasis')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Explore Maldives section')
                ->description('Manage the homepage product-card heading, text, and images here.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('explore_kicker')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('explore_heading_line_one')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('explore_heading_emphasis')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Fieldset::make('Resorts card')
                        ->columns(1)
                        ->schema([
                            OptimizedImageUpload::make(
                                Forms\Components\FileUpload::make('resorts_card_image')->label('Card image'),
                                'home-pages/cards',
                                maxWidth: 1400,
                                maxHeight: 1000,
                                quality: 80,
                            ),
                            Forms\Components\Textarea::make('resorts_card_copy')
                                ->label('Card text')
                                ->rows(3)
                                ->placeholder('Private island escapes, overwater villas, and handpicked luxury stays.'),
                        ]),
                    Forms\Components\Fieldset::make('Guest houses card')
                        ->columns(1)
                        ->schema([
                            OptimizedImageUpload::make(
                                Forms\Components\FileUpload::make('guesthouses_card_image')->label('Card image'),
                                'home-pages/cards',
                                maxWidth: 1400,
                                maxHeight: 1000,
                                quality: 80,
                            ),
                            Forms\Components\Textarea::make('guesthouses_card_copy')
                                ->label('Card text')
                                ->rows(3)
                                ->placeholder('Local island stays for travellers seeking culture, value, and beach life.'),
                        ]),
                    Forms\Components\Fieldset::make('City hotels card')
                        ->columns(1)
                        ->schema([
                            OptimizedImageUpload::make(
                                Forms\Components\FileUpload::make('city_hotels_card_image')->label('Card image'),
                                'home-pages/cards',
                                maxWidth: 1400,
                                maxHeight: 1000,
                                quality: 80,
                            ),
                            Forms\Components\Textarea::make('city_hotels_card_copy')
                                ->label('Card text')
                                ->rows(3)
                                ->placeholder('Convenient Malé and airport-area stays for stopovers and short visits.'),
                        ]),
                    Forms\Components\Fieldset::make('Liveaboards card')
                        ->columns(1)
                        ->schema([
                            OptimizedImageUpload::make(
                                Forms\Components\FileUpload::make('liveaboards_card_image')->label('Card image'),
                                'home-pages/cards',
                                maxWidth: 1400,
                                maxHeight: 1000,
                                quality: 80,
                            ),
                            Forms\Components\Textarea::make('liveaboards_card_copy')
                                ->label('Card text')
                                ->rows(3)
                                ->placeholder('Ocean journeys designed around diving, surfing, and private charters.'),
                        ]),
                ]),
            Forms\Components\Section::make('Hero image')->schema([
                OptimizedImageUpload::make(
                    Forms\Components\FileUpload::make('hero_image'),
                    'home-pages',
                    maxWidth: 2200,
                    maxHeight: 1600,
                    quality: 82,
                )->helperText('Upload a wide homepage banner image. It will be optimized automatically.'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('hero_image')->label('Image'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('explore_heading_line_one')->label('Explore section')->limit(20),
                Tables\Columns\TextColumn::make('kicker')->limit(30),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('updated_at')->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomePages::route('/'),
            'create' => Pages\CreateHomePage::route('/create'),
            'edit' => Pages\EditHomePage::route('/{record}/edit'),
        ];
    }
}
