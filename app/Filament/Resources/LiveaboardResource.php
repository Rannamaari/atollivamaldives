<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
use App\Filament\Resources\LiveaboardResource\Pages;
use App\Models\Accommodation;
use App\Models\Facility;
use App\Support\OptimizedImageUpload;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LiveaboardResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Travel Products';

    protected static ?string $navigationLabel = 'Liveaboards';

    protected static ?string $modelLabel = 'liveaboard';

    protected static ?string $pluralModelLabel = 'liveaboards';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', AccommodationType::Liveaboard);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('type')->default(AccommodationType::Liveaboard->value),
            Forms\Components\Section::make('Liveaboard details')->columns(2)->schema([
                Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'inactive' => 'Inactive'])->default('draft')->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('vessel_name'),
                Forms\Components\TextInput::make('vessel_type'),
                Forms\Components\TextInput::make('previous_name'),
                Forms\Components\TagsInput::make('aliases')->columnSpanFull(),
                Forms\Components\TextInput::make('tagline'),
                Forms\Components\TextInput::make('island')->label('Route or region'),
                Forms\Components\Textarea::make('summary')->columnSpanFull(),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                Forms\Components\TextInput::make('atoll')->label('Atolls'),
                Forms\Components\TextInput::make('official_website')->url(),
                Forms\Components\TextInput::make('source_url')->url()->label('Verification source URL'),
                Forms\Components\Textarea::make('address')->label('Departure information')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Pricing & media')->columns(3)->schema([
                Forms\Components\TextInput::make('price_from')->numeric()->prefix('$'),
                Forms\Components\TextInput::make('currency')->default('USD')->maxLength(3),
                Forms\Components\Select::make('price_unit')->options([
                    'night' => 'Per night',
                    'trip' => 'Per trip',
                    'person' => 'Per person',
                ])->default('trip'),
                OptimizedImageUpload::make(
                    FileUpload::make('featured_image'),
                    'accommodations/featured',
                    maxWidth: 2000,
                    maxHeight: 1400,
                    quality: 82,
                )
                    ->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                        if (is_string($state) && str_starts_with($state, 'placeholders/')) {
                            $component->state(null);
                        }
                    })
                    ->helperText('Images are automatically resized and compressed for faster loading.')
                    ->columnSpanFull(),
                OptimizedImageUpload::make(
                    FileUpload::make('images'),
                    'accommodations',
                    maxWidth: 2000,
                    maxHeight: 1400,
                    quality: 82,
                )
                    ->multiple()
                    ->reorderable()
                    ->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                        if (! is_array($state)) {
                            return;
                        }

                        $component->state(
                            array_values(
                                array_filter(
                                    $state,
                                    fn (mixed $path): bool => is_string($path) && ! str_starts_with($path, 'placeholders/')
                                )
                            )
                        );
                    })
                    ->helperText('Gallery uploads are automatically optimized and saved as lighter web images.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('rating')->numeric()->minValue(0)->maxValue(5),
                Forms\Components\Select::make('facilities')
                    ->relationship('facilities', 'name')
                    ->multiple()
                    ->options(fn () => Facility::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->columnSpan(2),
            ]),
            Forms\Components\Section::make('Vessel specifications')->columns(3)->schema([
                Forms\Components\TextInput::make('cabins')->numeric(),
                Forms\Components\TextInput::make('maximum_guests')->numeric(),
                Forms\Components\TextInput::make('minimum_nights')->numeric(),
                Forms\Components\TextInput::make('length_meters')->numeric(),
                Forms\Components\TextInput::make('cruising_speed_knots')->numeric(),
                Forms\Components\TextInput::make('typical_trip_length'),
                Forms\Components\TextInput::make('departure_port'),
                Forms\Components\Textarea::make('typical_route')->columnSpanFull(),
                Forms\Components\Toggle::make('diving_available'),
                Forms\Components\Toggle::make('surfing_available'),
                Forms\Components\Toggle::make('snorkeling_available'),
                Forms\Components\Toggle::make('nitrox_available'),
                Forms\Components\Toggle::make('dhoni_available'),
                Forms\Components\Toggle::make('jacuzzi'),
                Forms\Components\Toggle::make('spa'),
                Forms\Components\Toggle::make('restaurant'),
                Forms\Components\Toggle::make('bar'),
                Forms\Components\Toggle::make('wifi'),
            ]),
            Forms\Components\Section::make('Publishing')->columns(3)->schema([
                Forms\Components\Toggle::make('verified'),
                Forms\Components\Toggle::make('published'),
                Forms\Components\Toggle::make('featured'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]),
            Forms\Components\Section::make('SEO')->collapsed()->schema([
                Forms\Components\TextInput::make('seo_title'),
                Forms\Components\Textarea::make('seo_description'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')->label('Image'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('atoll')->label('Atolls')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('price_from')->money(fn ($record) => $record->currency),
                Tables\Columns\IconColumn::make('verified')->boolean(),
                Tables\Columns\IconColumn::make('featured')->boolean(),
                Tables\Columns\IconColumn::make('published')->boolean(),
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
            'index' => Pages\ListLiveaboards::route('/'),
            'create' => Pages\CreateLiveaboard::route('/create'),
            'edit' => Pages\EditLiveaboard::route('/{record}/edit'),
        ];
    }
}
