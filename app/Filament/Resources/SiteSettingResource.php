<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use App\Support\OptimizedImageUpload;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'SEO';
    protected static ?string $navigationLabel = 'Website Settings';
    protected static ?string $modelLabel = 'website setting';
    protected static ?string $pluralModelLabel = 'website settings';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Homepage hero banner')->schema([
                OptimizedImageUpload::make(
                    Forms\Components\FileUpload::make('hero_image'),
                    'site',
                    maxWidth: 2200,
                    maxHeight: 1600,
                    quality: 82,
                )
                    ->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                        if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                            $component->state(null);
                        }
                    })
                    ->helperText('Upload the homepage hero banner image. Recommended: wide landscape image.'),
            ]),
            Forms\Components\Section::make('SEO defaults')->columns(2)->schema([
                Forms\Components\TextInput::make('site_name')
                    ->default('Atolliva Maldives')
                    ->required(),
                Forms\Components\TextInput::make('default_meta_title')
                    ->default('Maldives Travel Agency | Resorts, Guesthouses & Holiday Packages | Atolliva Maldives'),
                Forms\Components\Textarea::make('default_meta_description')
                    ->rows(3)
                    ->default('Discover handpicked Maldives resorts, guesthouses, liveaboards and personalised holiday packages with local travel experts at Atolliva Maldives.')
                    ->columnSpanFull(),
                OptimizedImageUpload::make(
                    Forms\Components\FileUpload::make('default_og_image'),
                    'site/seo',
                    maxWidth: 1600,
                    maxHeight: 1600,
                    quality: 82,
                )->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                    if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                        $component->state(null);
                    }
                }),
                Forms\Components\Toggle::make('default_robots_index')->default(true),
                Forms\Components\Toggle::make('default_robots_follow')->default(true),
            ]),
            Forms\Components\Section::make('Business details')->columns(2)->schema([
                OptimizedImageUpload::make(
                    Forms\Components\FileUpload::make('business_logo'),
                    'site/branding',
                    maxWidth: 1200,
                    maxHeight: 1200,
                    quality: 86,
                )->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                    if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                        $component->state(null);
                    }
                }),
                Forms\Components\Textarea::make('company_description')
                    ->rows(4),
                Forms\Components\TextInput::make('business_email')
                    ->email()
                    ->default('hello@atollivamaldives.com'),
                Forms\Components\TextInput::make('business_phone')
                    ->default('+960 9996210'),
                Forms\Components\TextInput::make('business_secondary_phone')
                    ->default('+960 7779493'),
                Forms\Components\Textarea::make('business_address')
                    ->rows(3)
                    ->default("M. Ithaamuiyge 1\nAliasmagu\nMaldives")
                    ->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Social & verification')->columns(2)->schema([
                Forms\Components\TextInput::make('facebook_url')->url(),
                Forms\Components\TextInput::make('instagram_url')->url(),
                Forms\Components\TextInput::make('x_url')->url()->label('X / Twitter URL'),
                Forms\Components\TextInput::make('tiktok_url')->url(),
                Forms\Components\TextInput::make('google_analytics_id')
                    ->helperText('Example: G-XXXXXXXXXX'),
                Forms\Components\TextInput::make('google_tag_manager_id')
                    ->helperText('Example: GTM-XXXXXXX'),
                Forms\Components\TextInput::make('google_search_console_verification')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('hero_image')->label('Hero image')->getStateUsing(fn (SiteSetting $record): string => $record->hero_image_url),
            Tables\Columns\TextColumn::make('site_name')->label('Site name'),
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
