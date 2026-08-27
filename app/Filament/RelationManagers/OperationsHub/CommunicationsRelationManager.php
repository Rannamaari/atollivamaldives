<?php

namespace App\Filament\RelationManagers\OperationsHub;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Models\AgencyContact;
use App\Models\AgencyPartner;
use App\Models\EmailTemplate;
use App\Models\RateRequest;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Services\OperationsHub\TemplateRenderer;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CommunicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'communications';

    protected static ?string $title = 'Communication History';

    protected static ?string $modelLabel = 'communication history entry';

    protected static ?string $pluralModelLabel = 'Communication History';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
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
                        'supplier' => method_exists($this->ownerRecord, 'supplier') ? $this->ownerRecord->supplier : ($this->ownerRecord instanceof Supplier ? $this->ownerRecord : null),
                        'agency' => $this->ownerRecord instanceof AgencyPartner ? $this->ownerRecord : null,
                        'contact_name' => SupplierContact::find($get('supplier_contact_id'))?->full_name
                            ?: AgencyContact::find($get('agency_contact_id'))?->full_name,
                        'rate_request' => $this->ownerRecord instanceof RateRequest ? $this->ownerRecord : null,
                    ];

                    $rendered = app(TemplateRenderer::class)->render($template, $context);
                    $set('subject', $rendered['subject']);
                    $set('body', $rendered['body']);
                }),
            Forms\Components\Select::make('supplier_contact_id')->options(fn () => method_exists($this->ownerRecord, 'contacts') ? $this->ownerRecord->contacts()->pluck('full_name', 'id') : SupplierContact::query()->pluck('full_name', 'id'))->searchable(),
            Forms\Components\Select::make('agency_contact_id')->options(fn () => method_exists($this->ownerRecord, 'contacts') ? $this->ownerRecord->contacts()->pluck('full_name', 'id') : AgencyContact::query()->pluck('full_name', 'id'))->searchable(),
            Forms\Components\Select::make('direction')->options(CommunicationDirection::options())->required(),
            Forms\Components\Select::make('channel')->options(CommunicationChannel::options())->required(),
            Forms\Components\Select::make('status')->options(CommunicationStatus::options())->required()->default(CommunicationStatus::Draft->value),
            Forms\Components\TextInput::make('recipient'),
            Forms\Components\TextInput::make('subject')->columnSpanFull(),
            Forms\Components\Textarea::make('body')->rows(8)->columnSpanFull(),
            Forms\Components\Placeholder::make('placeholder_legend')
                ->label('Placeholder guide')
                ->content(fn () => $this->renderPlaceholderLegend())
                ->columnSpanFull(),
            Forms\Components\Placeholder::make('template_check')
                ->content(fn (Get $get) => $this->renderPlaceholderStatus(
                    app(TemplateRenderer::class)->findUnresolvedVariables(trim(($get('subject') ?? '').' '.($get('body') ?? '')))
                ))
                ->columnSpanFull(),
            Forms\Components\DateTimePicker::make('drafted_at'),
            Forms\Components\DateTimePicker::make('occurred_at'),
            Forms\Components\Toggle::make('follow_up_required')->default(false),
            Forms\Components\DateTimePicker::make('next_follow_up_at'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->columns([
                Tables\Columns\TextColumn::make('subject')->limit(36)->searchable(),
                Tables\Columns\TextColumn::make('channel')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? CommunicationChannel::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? CommunicationStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('occurred_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if ($this->ownerRecord instanceof Supplier) {
                            $data['supplier_id'] = $this->ownerRecord->id;
                        }

                        if ($this->ownerRecord instanceof AgencyPartner) {
                            $data['agency_partner_id'] = $this->ownerRecord->id;
                        }

                        if ($this->ownerRecord instanceof RateRequest) {
                            $data['rate_request_id'] = $this->ownerRecord->id;
                            $data['supplier_id'] = $this->ownerRecord->supplier_id;
                            $data['supplier_contact_id'] = $data['supplier_contact_id'] ?? $this->ownerRecord->supplier_contact_id;
                        }

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
                    ->visible(fn ($record) => $record->status === CommunicationStatus::Draft || $record->status === CommunicationStatus::Prepared)
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Toggle::make('follow_up_required')
                            ->default(fn ($record) => $record->follow_up_required)
                            ->live(),
                        Forms\Components\DateTimePicker::make('next_follow_up_at')
                            ->default(fn ($record) => $record->next_follow_up_at)
                            ->visible(fn (Get $get) => (bool) $get('follow_up_required')),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'status' => CommunicationStatus::SentManually,
                            'occurred_at' => $record->occurred_at ?? now(),
                            'logged_by' => $record->logged_by ?? auth()->id(),
                            'follow_up_required' => (bool) ($data['follow_up_required'] ?? false),
                            'next_follow_up_at' => ($data['follow_up_required'] ?? false) ? ($data['next_follow_up_at'] ?? $record->next_follow_up_at) : null,
                        ]);
                    }),
            ]);
    }

    protected function renderPlaceholderLegend(): HtmlString
    {
        $renderer = app(TemplateRenderer::class);
        $items = collect($renderer->placeholderCatalog())
            ->map(fn (array $meta, string $placeholder): string => sprintf(
                '<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 %s">%s <span class="ml-2 font-normal opacity-80">%s</span></span>',
                $this->toneClasses($meta['tone']),
                e($placeholder),
                e($meta['source'])
            ))
            ->implode(' ');

        return new HtmlString('<div class="flex flex-wrap gap-2">'.$items.'</div>');
    }

    protected function renderPlaceholderStatus(array $unresolved): HtmlString
    {
        if (blank($unresolved)) {
            return new HtmlString('<p class="text-sm text-emerald-700">All placeholders are filled. You can still edit the final email copy directly before sending it manually.</p>');
        }

        $items = collect(app(TemplateRenderer::class)->describePlaceholders($unresolved))
            ->map(fn (array $meta): string => sprintf(
                '<li><span class="font-semibold %s">%s</span> should come from <span class="font-medium text-gray-900">%s</span> using <span class="text-gray-600">%s</span>.</li>',
                $this->toneTextClasses($meta['tone']),
                e($meta['placeholder']),
                e($meta['source']),
                e($meta['field'])
            ))
            ->implode('');

        return new HtmlString('<div class="space-y-2"><p class="text-sm font-semibold text-amber-700">Some placeholders still need values.</p><ul class="list-disc space-y-1 pl-5 text-sm text-gray-700">'.$items.'</ul></div>');
    }

    protected function toneClasses(string $tone): string
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

    protected function toneTextClasses(string $tone): string
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
