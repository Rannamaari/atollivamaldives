<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageResource\Pages;
use App\Models\HomePage;
use App\Support\OptimizedImageUpload;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
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
                                FileUpload::make('resorts_card_image')->label('Card image'),
                                'home-pages/cards',
                                maxWidth: 1400,
                                maxHeight: 1000,
                                quality: 80,
                            )->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                                if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                                    $component->state(null);
                                }
                            }),
                            Forms\Components\Textarea::make('resorts_card_copy')
                                ->label('Card text')
                                ->rows(3)
                                ->placeholder('Private island escapes, overwater villas, and handpicked luxury stays.'),
                        ]),
                    Forms\Components\Fieldset::make('Guest houses card')
                        ->columns(1)
                        ->schema([
                            OptimizedImageUpload::make(
                                FileUpload::make('guesthouses_card_image')->label('Card image'),
                                'home-pages/cards',
                                maxWidth: 1400,
                                maxHeight: 1000,
                                quality: 80,
                            )->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                                if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                                    $component->state(null);
                                }
                            }),
                            Forms\Components\Textarea::make('guesthouses_card_copy')
                                ->label('Card text')
                                ->rows(3)
                                ->placeholder('Local island stays for travellers seeking culture, value, and beach life.'),
                        ]),
                    Forms\Components\Fieldset::make('City hotels card')
                        ->columns(1)
                        ->schema([
                            OptimizedImageUpload::make(
                                FileUpload::make('city_hotels_card_image')->label('Card image'),
                                'home-pages/cards',
                                maxWidth: 1400,
                                maxHeight: 1000,
                                quality: 80,
                            )->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                                if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                                    $component->state(null);
                                }
                            }),
                            Forms\Components\Textarea::make('city_hotels_card_copy')
                                ->label('Card text')
                                ->rows(3)
                                ->placeholder('Convenient Malé and airport-area stays for stopovers and short visits.'),
                        ]),
                    Forms\Components\Fieldset::make('Liveaboards card')
                        ->columns(1)
                        ->schema([
                            OptimizedImageUpload::make(
                                FileUpload::make('liveaboards_card_image')->label('Card image'),
                                'home-pages/cards',
                                maxWidth: 1400,
                                maxHeight: 1000,
                                quality: 80,
                            )->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                                if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                                    $component->state(null);
                                }
                            }),
                            Forms\Components\Textarea::make('liveaboards_card_copy')
                                ->label('Card text')
                                ->rows(3)
                                ->placeholder('Ocean journeys designed around diving, surfing, and private charters.'),
                        ]),
                ]),
            Forms\Components\Section::make('Arabic homepage content')
                ->description('This controls the Arabic version at /ar. Every field is editable here; the English homepage is not affected.')
                ->collapsed()
                ->schema([
                    Forms\Components\Section::make('Hero and stay finder')->compact()->columns(2)->schema([
                        Forms\Components\TextInput::make('arabic_kicker')->label('Hero kicker')->default(data_get(HomePage::arabicContentDefaults(), 'hero.kicker'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_heading_line_one')->label('Hero heading, line one')->default(data_get(HomePage::arabicContentDefaults(), 'hero.heading_line_one'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_heading_line_two')->label('Hero heading, line two')->default(data_get(HomePage::arabicContentDefaults(), 'hero.heading_line_two'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_heading_emphasis')->label('Hero heading emphasis')->default(data_get(HomePage::arabicContentDefaults(), 'hero.heading_emphasis'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\Textarea::make('arabic_description')->label('Hero description')->rows(3)->columnSpanFull()->default(data_get(HomePage::arabicContentDefaults(), 'hero.description'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.finder.eyebrow')->label('Finder eyebrow')->default(data_get(HomePage::arabicContentDefaults(), 'finder.eyebrow'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.finder.heading')->label('Finder heading')->default(data_get(HomePage::arabicContentDefaults(), 'finder.heading'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\Textarea::make('arabic_content.finder.description')->label('Finder description')->rows(2)->columnSpanFull()->default(data_get(HomePage::arabicContentDefaults(), 'finder.description'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.finder.destination_label')->label('Destination label')->default(data_get(HomePage::arabicContentDefaults(), 'finder.destination_label'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.finder.destination_placeholder')->label('Destination placeholder')->default(data_get(HomePage::arabicContentDefaults(), 'finder.destination_placeholder'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.finder.check_in')->label('Check-in label')->default(data_get(HomePage::arabicContentDefaults(), 'finder.check_in'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.finder.check_out')->label('Check-out label')->default(data_get(HomePage::arabicContentDefaults(), 'finder.check_out'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.finder.guests')->label('Guests label')->default(data_get(HomePage::arabicContentDefaults(), 'finder.guests'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.finder.search')->label('Search button')->default(data_get(HomePage::arabicContentDefaults(), 'finder.search'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                    ]),
                    Forms\Components\Section::make('Explore cards and introduction')->compact()->columns(2)->schema([
                        Forms\Components\TextInput::make('arabic_content.explore.kicker')->label('Explore kicker')->default(data_get(HomePage::arabicContentDefaults(), 'explore.kicker'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.explore.heading_line_one')->label('Explore heading')->default(data_get(HomePage::arabicContentDefaults(), 'explore.heading_line_one'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.explore.heading_emphasis')->label('Explore heading emphasis')->default(data_get(HomePage::arabicContentDefaults(), 'explore.heading_emphasis'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.intro.kicker')->label('Introduction kicker')->default(data_get(HomePage::arabicContentDefaults(), 'intro.kicker'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.intro.heading_line_one')->label('Introduction heading')->default(data_get(HomePage::arabicContentDefaults(), 'intro.heading_line_one'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.intro.heading_emphasis')->label('Introduction heading emphasis')->default(data_get(HomePage::arabicContentDefaults(), 'intro.heading_emphasis'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\Textarea::make('arabic_content.intro.description')->label('Introduction description')->rows(3)->columnSpanFull()->default(data_get(HomePage::arabicContentDefaults(), 'intro.description'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.intro.cta')->label('Introduction button')->default(data_get(HomePage::arabicContentDefaults(), 'intro.cta'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\Repeater::make('arabic_content.explore.labels')->label('Card labels')->default(data_get(HomePage::arabicContentDefaults(), 'explore.labels'))->simple()->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar'])->columnSpanFull(),
                        Forms\Components\Repeater::make('arabic_content.explore.copies')->label('Card descriptions')->default(data_get(HomePage::arabicContentDefaults(), 'explore.copies'))->simple()->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar'])->columnSpanFull(),
                    ]),
                    Forms\Components\Section::make('Why book with Atolliva')->compact()->schema([
                        Forms\Components\TextInput::make('arabic_content.benefits_heading')->label('Section heading')->default(data_get(HomePage::arabicContentDefaults(), 'benefits_heading'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\Repeater::make('arabic_content.benefits')
                            ->label('Benefits')
                            ->default(data_get(HomePage::arabicContentDefaults(), 'benefits'))
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Title')->required()->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                                Forms\Components\Textarea::make('description')->label('Description')->rows(3)->required()->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
                    Forms\Components\Section::make('Travel products, experiences, and blog')->compact()->columns(2)->schema([
                        Forms\Components\TextInput::make('arabic_content.products.kicker')->label('Products kicker')->default(data_get(HomePage::arabicContentDefaults(), 'products.kicker'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.products.heading_line_one')->label('Products heading')->default(data_get(HomePage::arabicContentDefaults(), 'products.heading_line_one'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.products.heading_emphasis')->label('Products heading emphasis')->default(data_get(HomePage::arabicContentDefaults(), 'products.heading_emphasis'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.products.explore_cta')->label('Products button')->default(data_get(HomePage::arabicContentDefaults(), 'products.explore_cta'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\Textarea::make('arabic_content.products.description')->label('Products description')->rows(3)->columnSpanFull()->default(data_get(HomePage::arabicContentDefaults(), 'products.description'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.experience.kicker')->label('Experiences kicker')->default(data_get(HomePage::arabicContentDefaults(), 'experience.kicker'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.experience.heading_line_one')->label('Experiences heading')->default(data_get(HomePage::arabicContentDefaults(), 'experience.heading_line_one'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.experience.heading_emphasis')->label('Experiences heading emphasis')->default(data_get(HomePage::arabicContentDefaults(), 'experience.heading_emphasis'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.experience.cta')->label('Experiences button')->default(data_get(HomePage::arabicContentDefaults(), 'experience.cta'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\Textarea::make('arabic_content.experience.description')->label('Experiences description')->rows(3)->columnSpanFull()->default(data_get(HomePage::arabicContentDefaults(), 'experience.description'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.journal.kicker')->label('Blog kicker')->default(data_get(HomePage::arabicContentDefaults(), 'journal.kicker'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.journal.heading_line_one')->label('Blog heading')->default(data_get(HomePage::arabicContentDefaults(), 'journal.heading_line_one'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.journal.heading_emphasis')->label('Blog heading emphasis')->default(data_get(HomePage::arabicContentDefaults(), 'journal.heading_emphasis'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.journal.cta')->label('View blog button')->default(data_get(HomePage::arabicContentDefaults(), 'journal.cta'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                    ]),
                    Forms\Components\Section::make('Inquiry and final call to action')->compact()->columns(2)->schema([
                        Forms\Components\TextInput::make('arabic_content.inquiry.kicker')->label('Inquiry kicker')->default(data_get(HomePage::arabicContentDefaults(), 'inquiry.kicker'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.inquiry.heading_line_one')->label('Inquiry heading')->default(data_get(HomePage::arabicContentDefaults(), 'inquiry.heading_line_one'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.inquiry.heading_emphasis')->label('Inquiry heading emphasis')->default(data_get(HomePage::arabicContentDefaults(), 'inquiry.heading_emphasis'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.inquiry.submit')->label('Inquiry button')->default(data_get(HomePage::arabicContentDefaults(), 'inquiry.submit'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\Textarea::make('arabic_content.inquiry.description')->label('Inquiry description')->rows(3)->columnSpanFull()->default(data_get(HomePage::arabicContentDefaults(), 'inquiry.description'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.closing.kicker')->label('Final CTA kicker')->default(data_get(HomePage::arabicContentDefaults(), 'closing.kicker'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.closing.heading_line_one')->label('Final CTA heading')->default(data_get(HomePage::arabicContentDefaults(), 'closing.heading_line_one'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.closing.heading_emphasis')->label('Final CTA heading emphasis')->default(data_get(HomePage::arabicContentDefaults(), 'closing.heading_emphasis'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                        Forms\Components\TextInput::make('arabic_content.closing.cta')->label('Final CTA button')->default(data_get(HomePage::arabicContentDefaults(), 'closing.cta'))->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                    ]),
                ]),
            Forms\Components\Section::make('Hero image')->schema([
                OptimizedImageUpload::make(
                    FileUpload::make('hero_image'),
                    'home-pages',
                    maxWidth: 2200,
                    maxHeight: 1600,
                    quality: 82,
                )
                    ->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                        if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                            $component->state(null);
                        }
                    })
                    ->helperText('Upload a wide homepage banner image. It will be optimized automatically.'),
            ]),
            Forms\Components\Section::make('Beyond the Blue image')
                ->description('This image appears next to the “Come for the islands. Remember the feeling.” section on the homepage.')
                ->schema([
                    OptimizedImageUpload::make(
                        FileUpload::make('experience_image')->label('Section image'),
                        'home-pages/experiences',
                        maxWidth: 1800,
                        maxHeight: 1400,
                        quality: 82,
                    )
                        ->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                            if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                                $component->state(null);
                            }
                        })
                        ->helperText('Upload a landscape image. The current image remains in place until you choose a replacement.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('hero_image')->label('Image')->getStateUsing(fn (HomePage $record): string => $record->hero_image_url),
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
