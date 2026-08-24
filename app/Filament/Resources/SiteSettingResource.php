<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Homepage Settings';
    protected static ?string $modelLabel = 'Homepage setting';
    protected static ?string $pluralModelLabel = 'Homepage settings';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Hero banner')->schema([
                Forms\Components\FileUpload::make('hero_image')
                    ->image()
                    ->disk('public')
                    ->directory('site')
                    ->imageEditor()
                    ->helperText('Upload the homepage hero banner image. Recommended: wide landscape image.'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('hero_image')->label('Hero image'),
            Tables\Columns\TextColumn::make('updated_at')->since()->label('Last updated'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSetting::route('/create'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
