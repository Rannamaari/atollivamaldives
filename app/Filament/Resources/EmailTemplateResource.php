<?php

namespace App\Filament\Resources;

use App\Enums\EmailTemplateType;
use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use App\Services\OperationsHub\TemplateRenderer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Email Templates';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Template')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    Forms\Components\Select::make('template_type')->options(EmailTemplateType::options())->required(),
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\TextInput::make('subject_template')->required()->columnSpanFull(),
                    Forms\Components\Textarea::make('body_template')->rows(12)->required()->columnSpanFull(),
                    Forms\Components\Textarea::make('description')->columnSpanFull(),
                    Forms\Components\Placeholder::make('available_placeholders')
                        ->label('Available placeholders')
                        ->content(fn () => static::renderPlaceholderLegend())
                        ->columnSpanFull(),
                    Forms\Components\Placeholder::make('template_preview')
                        ->label('Preview check')
                        ->content(fn (Get $get) => static::renderPlaceholderStatus(
                            app(TemplateRenderer::class)->findUnresolvedVariables(trim(($get('subject_template') ?? '').' '.($get('body_template') ?? '')))
                        ))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    protected static function renderPlaceholderLegend(): HtmlString
    {
        $renderer = app(TemplateRenderer::class);
        $groups = collect($renderer->placeholderCatalog())->groupBy('source');

        $html = $groups->map(function ($placeholders, $source): string {
            $items = collect($placeholders)->map(function (array $meta, string $placeholder): string {
                return sprintf(
                    '<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 %s">%s <span class="ml-2 font-normal opacity-80">%s</span></span>',
                    static::toneClasses($meta['tone']),
                    e($placeholder),
                    e($meta['field'])
                );
            })->implode(' ');

            return sprintf(
                '<div class="space-y-2"><p class="text-sm font-semibold text-gray-900">%s</p><div class="flex flex-wrap gap-2">%s</div></div>',
                e($source),
                $items
            );
        })->implode('');

        return new HtmlString('<div class="space-y-4">'.$html.'</div>');
    }

    protected static function renderPlaceholderStatus(array $unresolved): HtmlString
    {
        if (blank($unresolved)) {
            return new HtmlString('<p class="text-sm text-emerald-700">All placeholders are valid. Any highlighted variable here can be filled from the mapped source above.</p>');
        }

        $items = collect(app(TemplateRenderer::class)->describePlaceholders($unresolved))
            ->map(fn (array $meta): string => sprintf(
                '<li><span class="font-semibold %s">%s</span> comes from <span class="font-medium text-gray-900">%s</span> using <span class="text-gray-600">%s</span>.</li>',
                static::toneTextClasses($meta['tone']),
                e($meta['placeholder']),
                e($meta['source']),
                e($meta['field'])
            ))
            ->implode('');

        return new HtmlString('<div class="space-y-2"><p class="text-sm font-semibold text-amber-700">These placeholders still need values.</p><ul class="list-disc space-y-1 pl-5 text-sm text-gray-700">'.$items.'</ul></div>');
    }

    protected static function toneClasses(string $tone): string
    {
        return match ($tone) {
            'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'sky' => 'bg-sky-50 text-sky-700 ring-sky-200',
            'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'rose' => 'bg-rose-50 text-rose-700 ring-rose-200',
            'teal' => 'bg-teal-50 text-teal-700 ring-teal-200',
            'violet' => 'bg-violet-50 text-violet-700 ring-violet-200',
            default => 'bg-slate-50 text-slate-700 ring-slate-200',
        };
    }

    protected static function toneTextClasses(string $tone): string
    {
        return match ($tone) {
            'emerald' => 'text-emerald-700',
            'sky' => 'text-sky-700',
            'amber' => 'text-amber-700',
            'rose' => 'text-rose-700',
            'teal' => 'text-teal-700',
            'violet' => 'text-violet-700',
            default => 'text-slate-700',
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('template_type')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? EmailTemplateType::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('template_type')->options(EmailTemplateType::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
