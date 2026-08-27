<?php

namespace App\Filament\Resources;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Filament\Resources\CommunicationResource\Pages;
use App\Models\AgencyContact;
use App\Models\AgencyPartner;
use App\Models\Communication;
use App\Models\EmailTemplate;
use App\Models\RateRequest;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Services\OperationsHub\TemplateRenderer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CommunicationResource extends Resource
{
    protected static ?string $model = Communication::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Communications';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Communication')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('supplier_id')->relationship('supplier', 'legal_name')->searchable()->preload(),
                    Forms\Components\Select::make('agency_partner_id')->relationship('agencyPartner', 'legal_company_name')->searchable()->preload(),
                    Forms\Components\Select::make('supplier_contact_id')->options(fn (Get $get) => SupplierContact::query()->where('supplier_id', $get('supplier_id'))->pluck('full_name', 'id'))->searchable(),
                    Forms\Components\Select::make('agency_contact_id')->options(fn (Get $get) => AgencyContact::query()->where('agency_partner_id', $get('agency_partner_id'))->pluck('full_name', 'id'))->searchable(),
                    Forms\Components\Select::make('rate_request_id')->relationship('rateRequest', 'request_title')->searchable()->preload(),
                    Forms\Components\Select::make('template_picker')
                        ->label('Email template')
                        ->options(fn () => EmailTemplate::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                        ->live()
                        ->dehydrated(false)
                        ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                            $template = EmailTemplate::find($state);

                            if (! $template) {
                                return;
                            }

                            $context = [
                                'supplier' => $get('supplier_id') ? Supplier::find($get('supplier_id')) : null,
                                'agency' => $get('agency_partner_id') ? AgencyPartner::find($get('agency_partner_id')) : null,
                                'contact_name' => SupplierContact::find($get('supplier_contact_id'))?->full_name
                                    ?: AgencyContact::find($get('agency_contact_id'))?->full_name,
                                'rate_request' => $get('rate_request_id') ? RateRequest::find($get('rate_request_id')) : null,
                            ];

                            $rendered = app(TemplateRenderer::class)->render($template, $context);
                            $set('subject', $rendered['subject']);
                            $set('body', $rendered['body']);
                        }),
                    Forms\Components\Select::make('direction')->options(CommunicationDirection::options())->required(),
                    Forms\Components\Select::make('channel')->options(CommunicationChannel::options())->required(),
                    Forms\Components\Select::make('status')->options(CommunicationStatus::options())->required()->default(CommunicationStatus::Draft->value),
                    Forms\Components\TextInput::make('recipient'),
                    Forms\Components\TextInput::make('subject')->columnSpanFull(),
                    Forms\Components\Textarea::make('body')->rows(10)->columnSpanFull(),
                    Forms\Components\Placeholder::make('placeholder_legend')
                        ->label('Placeholder guide')
                        ->content(fn () => static::renderPlaceholderLegend())
                        ->columnSpanFull(),
                    Forms\Components\Placeholder::make('template_check')
                        ->content(fn (Get $get) => static::renderPlaceholderStatus(
                            app(TemplateRenderer::class)->findUnresolvedVariables(trim(($get('subject') ?? '').' '.($get('body') ?? '')))
                        ))
                        ->columnSpanFull(),
                    Forms\Components\DateTimePicker::make('drafted_at'),
                    Forms\Components\DateTimePicker::make('occurred_at'),
                    Forms\Components\Toggle::make('follow_up_required')->default(false),
                    Forms\Components\DateTimePicker::make('next_follow_up_at'),
                    Forms\Components\FileUpload::make('attachment_paths')
                        ->multiple()
                        ->disk('local')
                        ->directory('operations/communications')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('subject')->limit(40)->searchable(),
                Tables\Columns\TextColumn::make('channel')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? CommunicationChannel::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('direction')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? CommunicationDirection::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? CommunicationStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('supplier.trading_name')->label('Supplier')->toggleable(),
                Tables\Columns\TextColumn::make('agencyPartner.trading_name')->label('Agency')->toggleable(),
                Tables\Columns\TextColumn::make('occurred_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')->options(CommunicationChannel::options()),
                Tables\Filters\SelectFilter::make('status')->options(CommunicationStatus::options()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['logged_by'] = auth()->id();

                        if (($data['status'] ?? null) !== CommunicationStatus::Draft->value && blank($data['occurred_at'] ?? null)) {
                            $data['occurred_at'] = now();
                        }

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_sent')
                    ->label('Mark sent')
                    ->visible(fn (Communication $record) => $record->status === CommunicationStatus::Draft || $record->status === CommunicationStatus::Prepared)
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Toggle::make('follow_up_required')
                            ->default(fn (Communication $record) => $record->follow_up_required)
                            ->live(),
                        Forms\Components\DateTimePicker::make('next_follow_up_at')
                            ->default(fn (Communication $record) => $record->next_follow_up_at)
                            ->visible(fn (Get $get) => (bool) $get('follow_up_required')),
                    ])
                    ->action(function (Communication $record, array $data): void {
                        $record->update([
                            'status' => CommunicationStatus::SentManually,
                            'occurred_at' => $record->occurred_at ?? now(),
                            'logged_by' => $record->logged_by ?? auth()->id(),
                            'follow_up_required' => (bool) ($data['follow_up_required'] ?? false),
                            'next_follow_up_at' => ($data['follow_up_required'] ?? false) ? ($data['next_follow_up_at'] ?? $record->next_follow_up_at) : null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Communication marked sent')
                            ->body('The related contact timeline has been updated and follow-up reminders will appear when needed.')
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommunications::route('/'),
            'create' => Pages\CreateCommunication::route('/create'),
            'edit' => Pages\EditCommunication::route('/{record}/edit'),
        ];
    }

    protected static function renderPlaceholderLegend(): HtmlString
    {
        $renderer = app(TemplateRenderer::class);
        $items = collect($renderer->placeholderCatalog())
            ->map(fn (array $meta, string $placeholder): string => sprintf(
                '<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 %s">%s <span class="ml-2 font-normal opacity-80">%s</span></span>',
                static::toneClasses($meta['tone']),
                e($placeholder),
                e($meta['source'])
            ))
            ->implode(' ');

        return new HtmlString('<div class="flex flex-wrap gap-2">'.$items.'</div>');
    }

    protected static function renderPlaceholderStatus(array $unresolved): HtmlString
    {
        if (blank($unresolved)) {
            return new HtmlString('<p class="text-sm text-emerald-700">All placeholders are filled. If you still want to personalize the message, you can edit the subject and body directly.</p>');
        }

        $items = collect(app(TemplateRenderer::class)->describePlaceholders($unresolved))
            ->map(fn (array $meta): string => sprintf(
                '<li><span class="font-semibold %s">%s</span> should come from <span class="font-medium text-gray-900">%s</span> using <span class="text-gray-600">%s</span>.</li>',
                static::toneTextClasses($meta['tone']),
                e($meta['placeholder']),
                e($meta['source']),
                e($meta['field'])
            ))
            ->implode('');

        return new HtmlString('<div class="space-y-2"><p class="text-sm font-semibold text-amber-700">Some placeholders still need values.</p><ul class="list-disc space-y-1 pl-5 text-sm text-gray-700">'.$items.'</ul></div>');
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
}
