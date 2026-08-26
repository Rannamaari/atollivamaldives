<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogOfferResource\Pages;
use App\Models\BlogCategory;
use App\Models\BlogOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogOfferResource extends Resource
{
    protected static ?string $model = BlogOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Blog Offers';

    protected static ?string $modelLabel = 'Blog offer';

    protected static ?string $pluralModelLabel = 'Blog offers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Offer details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('eyebrow')
                        ->maxLength(255)
                        ->helperText('Small label above the offer title.'),
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->disk('public')
                        ->directory('blog-offers')
                        ->imageEditor()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('button_text')
                        ->default('Explore offer')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('button_url')
                        ->placeholder('https://example.com or /stays')
                        ->helperText('You can link to a stay, liveaboards page, package, or WhatsApp URL.'),
                    Forms\Components\Select::make('target_categories')
                        ->label('Show for categories')
                        ->multiple()
                        ->options(fn () => BlogCategory::query()->where('active', true)->orderBy('sort_order')->pluck('name', 'name'))
                        ->searchable()
                        ->preload()
                        ->helperText('Leave empty to allow this offer on any blog post. Select one or more categories to target it to matching posts.')
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Visibility')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('active')
                        ->default(true),
                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Image'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('eyebrow')->toggleable(),
                Tables\Columns\TextColumn::make('target_categories')
                    ->badge()
                    ->separator(', ')
                    ->label('Categories')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('button_text')->label('CTA'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('active')->boolean(),
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
            'index' => Pages\ListBlogOffers::route('/'),
            'create' => Pages\CreateBlogOffer::route('/create'),
            'edit' => Pages\EditBlogOffer::route('/{record}/edit'),
        ];
    }
}
