<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\BlogCategory;
use App\Models\BlogOffer;
use App\Models\Post;
use App\Support\OptimizedImageUpload;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Article')->schema([
                Forms\Components\TextInput::make('title')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Select::make('category')
                    ->options(fn () => BlogCategory::query()->orderBy('sort_order')->pluck('name', 'name'))
                    ->searchable()
                    ->preload()
                    ->helperText('Manage the available category list under Content → Blog Categories.'),
                Forms\Components\Select::make('blog_offer_id')
                    ->label('Specific blog offer')
                    ->options(fn () => BlogOffer::query()->orderBy('sort_order')->pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->helperText('Optional. Choose a specific offer for this post. If left blank, the website will try category-matched offers first, then general offers.'),
                Forms\Components\Textarea::make('excerpt'),
                Forms\Components\RichEditor::make('body')->required()->columnSpanFull(),
                OptimizedImageUpload::make(
                    FileUpload::make('featured_image'),
                    'blog',
                    maxWidth: 1800,
                    maxHeight: 1200,
                    quality: 82,
                )
                    ->helperText('Upload a JPG, PNG, or WebP image. It will be automatically resized and compressed for the website.')
                    ->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                        if (! is_string($state)) {
                            return;
                        }

                        // Older seeded posts used remote image URLs, but Filament's file upload
                        // field expects a local stored path when editing records.
                        if (str_starts_with($state, 'http://') || str_starts_with($state, 'https://')) {
                            $component->state(null);
                        }
                    }),
                Forms\Components\TextInput::make('author')->default('Atolliva Maldives'),
            ])->columns(2),
            Forms\Components\Section::make('Publishing')->columns(3)->schema([Forms\Components\Toggle::make('published'), Forms\Components\Toggle::make('featured'), Forms\Components\DateTimePicker::make('published_at')]),
            Forms\Components\Section::make('SEO')->collapsed()->schema([Forms\Components\TextInput::make('seo_title'), Forms\Components\Textarea::make('seo_description')]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\ImageColumn::make('featured_image'), Tables\Columns\TextColumn::make('title')->searchable()->sortable(), Tables\Columns\TextColumn::make('category')->badge(), Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(), Tables\Columns\IconColumn::make('published')->boolean()])->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPosts::route('/'), 'create' => Pages\CreatePost::route('/create'), 'edit' => Pages\EditPost::route('/{record}/edit')];
    }
}
