<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
use App\Filament\Resources\AccommodationResource\Pages;
use App\Models\Accommodation;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AccommodationResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Travel Products';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'All Travel Products';

    protected static ?string $modelLabel = 'travel product';

    protected static ?string $pluralModelLabel = 'travel products';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Travel product details')->columns(2)->schema([
                Forms\Components\Select::make('type')->options(collect(AccommodationType::cases())->mapWithKeys(fn ($x) => [$x->value => $x->label()]))->required(),
                Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'inactive' => 'Inactive'])->default('draft')->required(),
                Forms\Components\TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true), Forms\Components\TextInput::make('previous_name'),
                Forms\Components\TagsInput::make('aliases')->columnSpanFull(), Forms\Components\TextInput::make('tagline'),
                Forms\Components\Textarea::make('summary')->columnSpanFull(), Forms\Components\RichEditor::make('description')->columnSpanFull(),
                Forms\Components\TextInput::make('island'), Forms\Components\TextInput::make('atoll'), Forms\Components\TextInput::make('city'), Forms\Components\TextInput::make('country')->default('Maldives'), Forms\Components\TextInput::make('property_subtype')->label('Subtype / classification'), Forms\Components\TextInput::make('official_website')->url(), Forms\Components\TextInput::make('source_url')->url()->label('Verification source URL'), Forms\Components\Textarea::make('address')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Pricing & media')->columns(3)->schema([
                Forms\Components\TextInput::make('price_from')->numeric()->prefix('$'), Forms\Components\TextInput::make('currency')->default('USD')->maxLength(3), Forms\Components\Select::make('price_unit')->options(['night' => 'Per night', 'trip' => 'Per trip', 'person' => 'Per person']),
                FileUpload::make('featured_image')
                    ->image()
                    ->directory('accommodations/featured')
                    ->disk('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                        if (is_string($state) && str_starts_with($state, 'placeholders/')) {
                            $component->state(null);
                        }
                    })
                    ->columnSpanFull(),
                FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->directory('accommodations')
                    ->disk('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
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
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('rating')->numeric()->minValue(0)->maxValue(5),
                Forms\Components\Select::make('facilities')->relationship('facilities', 'name')->multiple()->options(fn () => Facility::query()->orderBy('name')->pluck('name', 'id'))->searchable()->preload()->columnSpan(2),
            ]),
            Forms\Components\Section::make('Publishing')->columns(3)->schema([Forms\Components\Toggle::make('verified'), Forms\Components\Toggle::make('published'), Forms\Components\Toggle::make('featured'), Forms\Components\TextInput::make('airport_distance'), Forms\Components\TextInput::make('transfer_duration'), Forms\Components\TimePicker::make('check_in_time'), Forms\Components\TimePicker::make('check_out_time'), Forms\Components\TextInput::make('sort_order')->numeric()->default(0), Forms\Components\Textarea::make('transfer_notes')->columnSpanFull()]),
            Forms\Components\Section::make('SEO')->collapsed()->schema([Forms\Components\TextInput::make('seo_title'), Forms\Components\Textarea::make('seo_description')]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\ImageColumn::make('featured_image')->label('Image'), Tables\Columns\TextColumn::make('name')->searchable()->sortable(), Tables\Columns\TextColumn::make('type')->badge(), Tables\Columns\TextColumn::make('atoll')->searchable(), Tables\Columns\TextColumn::make('status')->badge(), Tables\Columns\TextColumn::make('price_from')->money(fn ($record) => $record->currency), Tables\Columns\IconColumn::make('verified')->boolean(), Tables\Columns\IconColumn::make('featured')->boolean(), Tables\Columns\IconColumn::make('published')->boolean()])->filters([Tables\Filters\SelectFilter::make('type')->options(collect(AccommodationType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()])), Tables\Filters\TernaryFilter::make('published')])->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAccommodations::route('/'), 'create' => Pages\CreateAccommodation::route('/create'), 'edit' => Pages\EditAccommodation::route('/{record}/edit')];
    }
}
