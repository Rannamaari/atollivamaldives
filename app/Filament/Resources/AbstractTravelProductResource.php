<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\Facility;
use App\Services\SocialImageGeneratorService;
use App\Services\SocialShareService;
use App\Support\OptimizedImageUpload;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
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
            Forms\Components\Section::make('SEO & Social')->collapsed()->schema([
                Forms\Components\TextInput::make('seo_title')
                    ->helperText('Optional. If left blank, Atolliva will create the page title automatically.'),
                Forms\Components\Textarea::make('seo_description')
                    ->helperText('Optional. If left blank, the summary and property details will be used.'),
                Forms\Components\TextInput::make('social_title')
                    ->maxLength(100)
                    ->helperText('Optional. Overrides the social share title only.'),
                Forms\Components\Textarea::make('social_description')
                    ->rows(3)
                    ->helperText('Optional. Overrides the social preview description only.')
                    ->maxLength(200),
                Forms\Components\Textarea::make('social_caption')
                    ->rows(5)
                    ->columnSpanFull()
                    ->helperText('Optional. Used for WhatsApp, native share, and Copy Caption.'),
                Forms\Components\Textarea::make('social_hashtags')
                    ->rows(2)
                    ->columnSpanFull()
                    ->helperText('Optional. Add hashtags separated by spaces or commas.'),
                OptimizedImageUpload::make(
                    FileUpload::make('social_image'),
                    'social/manual',
                    maxWidth: 1600,
                    maxHeight: 1200,
                    quality: 84,
                )
                    ->helperText('Optional manual social image. If blank, Atolliva will use a generated image or the featured image.'),
                Forms\Components\Placeholder::make('generated_social_image_preview')
                    ->label('Generated social image')
                    ->content(function (?Accommodation $record): HtmlString|string {
                        if (! $record?->generated_social_image) {
                            return 'No generated social image yet.';
                        }

                        $url = asset('storage/'.ltrim($record->generated_social_image, '/'));

                        return new HtmlString('<img src="'.e($url).'" alt="Generated social image preview" style="max-width: 22rem; border-radius: 1rem; border: 1px solid rgba(15, 23, 42, 0.12);" />');
                    }),
                Actions::make([
                    Action::make('generateSocialImage')
                        ->label('Generate Social Image')
                        ->action(function (?Accommodation $record): void {
                            if (! $record) {
                                return;
                            }

                            app(SocialImageGeneratorService::class)->generateAndStore($record, true);

                            Notification::make()
                                ->title('Social image generated')
                                ->success()
                                ->send();
                        }),
                ])->columnSpanFull(),
                Forms\Components\Placeholder::make('social_preview')
                    ->label('Share preview')
                    ->columnSpanFull()
                    ->content(function (?Accommodation $record): HtmlString|string {
                        if (! $record) {
                            return 'Save this travel product first to preview how it will appear when shared.';
                        }

                        $share = app(SocialShareService::class)->for($record)->toArray();

                        return new HtmlString(
                            '<div style="display:grid;gap:0;max-width:30rem;border:1px solid rgba(15,23,42,.12);border-radius:1rem;overflow:hidden;background:#fff;">'
                            .'<img src="'.e($share['image']).'" alt="" style="display:block;width:100%;aspect-ratio:1200 / 630;object-fit:cover;" />'
                            .'<div style="padding:1rem 1.1rem;display:grid;gap:.45rem;">'
                            .'<p style="margin:0;font-size:.75rem;color:#64748b;">atollivamaldives.com</p>'
                            .'<p style="margin:0;font-weight:700;color:#0f172a;">'.e($share['title']).'</p>'
                            .'<p style="margin:0;color:#475569;">'.e($share['description']).'</p>'
                            .'</div></div>'
                        );
                    }),
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
                    Tables\Actions\BulkAction::make('generateSocialImages')
                        ->label('Generate Social Images')
                        ->action(function ($records): void {
                            $generator = app(SocialImageGeneratorService::class);

                            foreach ($records as $record) {
                                $generator->generateAndStore($record, true);
                            }

                            Notification::make()
                                ->title('Social images generated')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
