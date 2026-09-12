<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiveaboardPageResource\Pages;
use App\Models\LiveaboardPage;
use App\Services\AutomaticTranslationService;
use App\Support\OptimizedImageUpload;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
                OptimizedImageUpload::make(
                    Forms\Components\FileUpload::make('hero_image'),
                    'liveaboards/hero',
                    maxWidth: 2200,
                    maxHeight: 1600,
                    quality: 82,
                )
                    ->afterStateHydrated(function (FileUpload $component, mixed $state): void {
                        if (is_string($state) && (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))) {
                            $component->state(null);
                        }
                    })
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('eyebrow')->required()->maxLength(255),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Textarea::make('intro')->rows(3)->columnSpanFull(),
                Forms\Components\RichEditor::make('body')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Arabic translation')
                ->description('Generate a Google Cloud Arabic draft, then review and edit it before it appears on /ar/liveaboards.')
                ->collapsed()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('arabic_eyebrow')->label('Arabic eyebrow')->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                    Forms\Components\TextInput::make('arabic_title')->label('Arabic title')->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                    Forms\Components\Textarea::make('arabic_intro')->label('Arabic introduction')->rows(3)->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar'])->columnSpanFull(),
                    Forms\Components\RichEditor::make('arabic_body')->label('Arabic story')->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar'])->columnSpanFull(),
                    Forms\Components\TextInput::make('arabic_contact_heading')->label('Arabic contact heading')->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                    Forms\Components\Textarea::make('arabic_contact_text')->label('Arabic contact text')->rows(3)->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                    Actions::make([
                        Action::make('generateMissingArabicDraft')
                            ->label('Generate Missing Arabic Draft')
                            ->icon('heroicon-o-language')
                            ->requiresConfirmation()
                            ->modalHeading('Generate the Arabic liveaboard page draft?')
                            ->modalDescription('Only empty Arabic fields will be filled. Existing Arabic edits will not be replaced.')
                            ->visible(fn (?LiveaboardPage $record): bool => $record !== null)
                            ->action(function (LiveaboardPage $record, Forms\Set $set): void {
                                try {
                                    $translator = app(AutomaticTranslationService::class);
                                    $updates = [];

                                    foreach ([
                                        'arabic_eyebrow' => [$record->eyebrow, false],
                                        'arabic_title' => [$record->title, false],
                                        'arabic_intro' => [$record->intro, false],
                                        'arabic_body' => [$record->body, true],
                                        'arabic_contact_heading' => [$record->contact_heading, false],
                                        'arabic_contact_text' => [$record->contact_text, false],
                                    ] as $field => [$source, $isHtml]) {
                                        if (blank($record->{$field}) && filled($source)) {
                                            $updates[$field] = $translator->translate((string) $source, 'ar', $isHtml);
                                        }
                                    }

                                    if ($updates === []) {
                                        Notification::make()->title('Arabic fields are already filled')->info()->send();

                                        return;
                                    }

                                    $record->update($updates);

                                    foreach ($updates as $field => $value) {
                                        $set($field, $value);
                                    }

                                    Notification::make()->title('Arabic liveaboard draft generated')->body('Review the draft, then save any refinements.')->success()->send();
                                } catch (\RuntimeException $exception) {
                                    Notification::make()->title('Arabic draft was not generated')->body($exception->getMessage())->danger()->send();
                                }
                            }),
                    ])->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Gallery')->schema([
                OptimizedImageUpload::make(
                    Forms\Components\FileUpload::make('gallery_images'),
                    'liveaboards/gallery',
                    maxWidth: 2000,
                    maxHeight: 1400,
                    quality: 82,
                )
                    ->multiple()
                    ->reorderable()
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
                Tables\Columns\ImageColumn::make('hero_image')->label('Hero')->getStateUsing(fn (LiveaboardPage $record): string => $record->hero_image_url),
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
