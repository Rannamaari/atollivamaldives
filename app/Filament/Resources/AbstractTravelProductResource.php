<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
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

abstract class AbstractTravelProductResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static ?string $navigationGroup = 'Travel Products';

    abstract protected static function getTravelProductType(): AccommodationType;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', static::getTravelProductType()->value);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('type')->default(static::getTravelProductType()->value),
            Forms\Components\Section::make(static::getNavigationLabel().' details')->columns(2)->schema([
                Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'inactive' => 'Inactive',
                ])->default('draft')->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('previous_name'),
                Forms\Components\TagsInput::make('aliases'),
                Forms\Components\TextInput::make('tagline'),
                Forms\Components\TextInput::make('island'),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('country')->default('Maldives'),
                Forms\Components\TextInput::make('atoll'),
                Forms\Components\TextInput::make('property_subtype')->label('Subtype / classification'),
                Forms\Components\TextInput::make('official_website')->url(),
                Forms\Components\TextInput::make('source_url')->url()->label('Verification source URL'),
                Forms\Components\Textarea::make('summary')->columnSpanFull(),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                Forms\Components\Textarea::make('address')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Pricing, media & highlights')->columns(3)->schema([
                Forms\Components\TextInput::make('price_from')->numeric()->prefix('$'),
                Forms\Components\TextInput::make('currency')->default('USD')->maxLength(3),
                Forms\Components\Select::make('price_unit')->options([
                    'night' => 'Per night',
                    'trip' => 'Per trip',
                    'person' => 'Per person',
                ])->default('night'),
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
            Forms\Components\Section::make('Operations & publishing')->columns(3)->schema([
                Forms\Components\Toggle::make('verified'),
                Forms\Components\Toggle::make('published'),
                Forms\Components\Toggle::make('featured'),
                Forms\Components\TextInput::make('airport_distance'),
                Forms\Components\TextInput::make('transfer_duration'),
                Forms\Components\TimePicker::make('check_in_time'),
                Forms\Components\TimePicker::make('check_out_time'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                Forms\Components\Textarea::make('transfer_notes')->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('atoll')->searchable(),
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
}
