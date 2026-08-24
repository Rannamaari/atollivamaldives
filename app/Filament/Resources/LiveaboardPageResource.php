<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiveaboardPageResource\Pages;
use App\Models\LiveaboardPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LiveaboardPageResource extends Resource
{
    protected static ?string $model = LiveaboardPage::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Liveaboard Landing Page';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Hero')->columns(2)->schema([
                Forms\Components\FileUpload::make('hero_image')
                    ->image()
                    ->disk('public')
                    ->directory('liveaboards/hero')
                    ->imageEditor()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('eyebrow')->required()->maxLength(255),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Textarea::make('intro')->rows(3)->columnSpanFull(),
                Forms\Components\RichEditor::make('body')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Gallery')->schema([
                Forms\Components\FileUpload::make('gallery_images')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->disk('public')
                    ->directory('liveaboards/gallery')
                    ->imageEditor()
                    ->helperText('Upload and reorder the liveaboard gallery images shown on the page.'),
            ]),
            Forms\Components\Section::make('Contact section')->schema([
                Forms\Components\TextInput::make('contact_heading')->required()->maxLength(255),
                Forms\Components\Textarea::make('contact_text')->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('hero_image')->label('Hero'),
                Tables\Columns\TextColumn::make('title')->limit(50),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Last updated'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLiveaboardPages::route('/'),
            'create' => Pages\CreateLiveaboardPage::route('/create'),
            'edit' => Pages\EditLiveaboardPage::route('/{record}/edit'),
        ];
    }
}
