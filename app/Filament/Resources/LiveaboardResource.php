<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
use App\Filament\Resources\LiveaboardResource\Pages;
use App\Models\Accommodation;
use Filament\Forms;
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', AccommodationType::Liveaboard);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('type')->default(AccommodationType::Liveaboard->value),
            Forms\Components\Section::make('Liveaboard details')->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('tagline'),
                Forms\Components\TextInput::make('island')->label('Route or region'),
                Forms\Components\Textarea::make('summary')->columnSpanFull(),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                Forms\Components\TextInput::make('atoll')->label('Atolls'),
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
                Forms\Components\FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->directory('accommodations')
                    ->disk('public')
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('amenities')->columnSpanFull(),
                Forms\Components\TextInput::make('rating')->numeric()->minValue(0)->maxValue(5),
            ]),
            Forms\Components\Section::make('Publishing')->columns(3)->schema([
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
                Tables\Columns\ImageColumn::make('images')->circular()->stacked(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('atoll')->label('Atolls')->searchable(),
                Tables\Columns\TextColumn::make('price_from')->money(fn ($record) => $record->currency),
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
