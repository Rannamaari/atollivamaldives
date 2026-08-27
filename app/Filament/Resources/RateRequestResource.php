<?php

namespace App\Filament\Resources;

use App\Enums\EmailTemplateType;
use App\Enums\RateRequestStatus;
use App\Filament\RelationManagers\OperationsHub\ActivityEventsRelationManager;
use App\Filament\RelationManagers\OperationsHub\CommunicationsRelationManager;
use App\Filament\RelationManagers\OperationsHub\DocumentsRelationManager;
use App\Filament\RelationManagers\OperationsHub\InternalNotesRelationManager;
use App\Filament\RelationManagers\OperationsHub\OperationsTasksRelationManager;
use App\Filament\Resources\RateRequestResource\Pages;
use App\Models\EmailTemplate;
use App\Models\RateRequest;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Services\OperationsHub\RateRequestWorkflow;
use App\Services\OperationsHub\TemplateRenderer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class RateRequestResource extends Resource
{
    protected static ?string $model = RateRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Rate Requests';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Rate request')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('supplier_id')
                        ->relationship('supplier', 'legal_name')
                        ->label('Supplier')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required()
                        ->native(false)
                        ->helperText('Choose the hotel, resort, liveaboard, or supplier you are requesting rates from.'),
                    Forms\Components\Select::make('supplier_contact_id')
                        ->label('Supplier contact')
                        ->options(fn (Get $get) => SupplierContact::query()->where('supplier_id', $get('supplier_id'))->pluck('full_name', 'id'))
                        ->searchable()
                        ->live()
                        ->native(false)
                        ->disabled(fn (Get $get) => blank($get('supplier_id')))
                        ->helperText('Optional, but recommended if you already know the reservations or sales contact.'),
                    Forms\Components\TextInput::make('request_title')->required(),
                    Forms\Components\Select::make('status')
                        ->options(RateRequestStatus::options())
                        ->required()
                        ->default(RateRequestStatus::Draft->value)
                        ->native(false),
                    Forms\Components\TextInput::make('requested_rate_period')
                        ->placeholder('e.g. Winter 2026 / Summer 2027')
                        ->helperText('Add the season or contract period you are requesting.'),
                    Forms\Components\TextInput::make('requested_markets')
                        ->placeholder('e.g. UK, Europe, Middle East'),
                    Forms\Components\Textarea::make('requested_services')
                        ->columnSpanFull()
                        ->rows(4)
                        ->placeholder('Room categories, meal plans, transfer rates, child policy, offers, commission structure...'),
                    Forms\Components\DateTimePicker::make('drafted_at')->default(now()),
                    Forms\Components\DateTimePicker::make('sent_at'),
                    Forms\Components\DateTimePicker::make('first_follow_up_at'),
                    Forms\Components\DateTimePicker::make('second_follow_up_at'),
                    Forms\Components\DateTimePicker::make('next_follow_up_at'),
                    Forms\Components\DateTimePicker::make('response_received_at'),
                    Forms\Components\Toggle::make('rates_received'),
                    Forms\Components\Toggle::make('agreement_received'),
                    Forms\Components\Select::make('assigned_to')
                        ->relationship('assignedUser', 'name')
                        ->searchable()
                        ->preload()
                        ->default(auth()->id())
                        ->native(false),
                    Forms\Components\Textarea::make('notes')->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Supplier email draft preview')
                ->description('This preview updates from the selected supplier, contact, and rate period so you can copy the email before sending it.')
                ->columns(1)
                ->schema([
                    Forms\Components\Select::make('email_template_preview_type')
                        ->label('Email template')
                        ->options(collect(EmailTemplateType::cases())->mapWithKeys(fn (EmailTemplateType $type) => [
                            $type->value => $type->label(),
                        ])->all())
                        ->default(EmailTemplateType::RequestB2BRates->value)
                        ->live()
                        ->native(false)
                        ->dehydrated(false)
                        ->helperText('Pick the template style you want to preview for this supplier.'),
                    Forms\Components\Placeholder::make('email_preview_subject')
                        ->label('Email subject')
                        ->content(fn (Get $get) => static::renderPreviewTextarea(
                            static::rateRequestEmailPreview($get)['subject'],
                            'Choose a supplier and rate period to generate the email subject preview.'
                        )),
                    Forms\Components\Placeholder::make('email_preview_body')
                        ->label('Email body')
                        ->content(fn (Get $get) => static::renderPreviewTextarea(
                            static::rateRequestEmailPreview($get)['body'],
                            'Choose a supplier and rate period to generate the email body preview.',
                            14
                        )),
                    Forms\Components\Placeholder::make('email_preview_notes')
                        ->label('Preview status')
                        ->content(fn (Get $get) => static::renderPreviewNotes(
                            static::rateRequestEmailPreview($get)['unresolved']
                        )),
                ]),
        ]);
    }

    protected static function rateRequestEmailPreview(Get $get): array
    {
        $supplier = filled($get('supplier_id')) ? Supplier::query()->find($get('supplier_id')) : null;

        if (! $supplier) {
            return [
                'subject' => null,
                'body' => null,
                'unresolved' => [],
            ];
        }

        $templateType = EmailTemplateType::tryFrom((string) ($get('email_template_preview_type') ?: EmailTemplateType::RequestB2BRates->value))
            ?? EmailTemplateType::RequestB2BRates;

        $template = EmailTemplate::query()
            ->where('template_type', $templateType->value)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        if (! $template) {
            return [
                'subject' => 'No active template found for '.$templateType->label().'.',
                'body' => 'Create or activate an email template for this type in the admin panel to generate a ready-to-copy draft.',
                'unresolved' => [],
            ];
        }

        $contact = filled($get('supplier_contact_id')) ? SupplierContact::query()->find($get('supplier_contact_id')) : null;
        $fallbackContact = $supplier->contacts()->where('is_primary', true)->first()
            ?: $supplier->contacts()->where('is_active', true)->first();

        $previewRateRequest = new RateRequest([
            'requested_rate_period' => $get('requested_rate_period'),
        ]);

        return app(TemplateRenderer::class)->render($template, [
            'supplier' => $supplier,
            'rate_request' => $previewRateRequest,
            'contact_name' => $contact?->full_name ?: $fallbackContact?->full_name,
        ]);
    }

    protected static function renderPreviewTextarea(?string $value, string $emptyMessage, int $rows = 3): HtmlString
    {
        $content = filled($value) ? $value : $emptyMessage;

        return new HtmlString(sprintf(
            '<textarea readonly rows="%d" class="fi-input block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm leading-6 text-gray-900 shadow-sm">%s</textarea>',
            $rows,
            e($content)
        ));
    }

    protected static function renderPreviewNotes(array $unresolved): HtmlString
    {
        if (blank($unresolved)) {
            return new HtmlString('<p class="text-sm text-gray-500">The preview is ready to copy. If you want to keep it in your communications log, use <strong>Prepare Email Draft</strong> after saving the rate request.</p>');
        }

        return new HtmlString('<p class="text-sm text-amber-600">Some placeholders still need data: '.e(implode(', ', $unresolved)).'. Add the missing details or choose a contact to complete the draft.</p>');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('request_title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier_display_name')
                    ->label('Supplier')
                    ->state(fn (RateRequest $record) => $record->supplier?->trading_name ?: $record->supplier?->legal_name)
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('supplier', function ($supplierQuery) use ($search): void {
                            $supplierQuery
                                ->where('trading_name', 'like', "%{$search}%")
                                ->orWhere('legal_name', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? RateRequestStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('sent_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('next_follow_up_at')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('rates_received')->boolean(),
                Tables\Columns\IconColumn::make('agreement_received')->boolean(),
                Tables\Columns\TextColumn::make('assignedUser.name')->label('Assigned'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(RateRequestStatus::options()),
                Tables\Filters\SelectFilter::make('assigned_to')->relationship('assignedUser', 'name'),
                Tables\Filters\Filter::make('follow_up_due')->query(fn ($query) => $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('mark_ready')
                    ->label('Mark ready')
                    ->requiresConfirmation()
                    ->action(fn (RateRequest $record) => app(RateRequestWorkflow::class)->markReady($record)),
                Action::make('mark_sent')
                    ->label('Mark sent')
                    ->requiresConfirmation()
                    ->action(fn (RateRequest $record) => app(RateRequestWorkflow::class)->markSent($record)),
                Action::make('record_response')
                    ->label('Record response')
                    ->form([
                        Forms\Components\Toggle::make('rates_received'),
                        Forms\Components\Toggle::make('agreement_received'),
                    ])
                    ->action(fn (array $data, RateRequest $record) => app(RateRequestWorkflow::class)->recordResponse($record, (bool) ($data['rates_received'] ?? false), (bool) ($data['agreement_received'] ?? false))),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommunicationsRelationManager::class,
            OperationsTasksRelationManager::class,
            InternalNotesRelationManager::class,
            DocumentsRelationManager::class,
            ActivityEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRateRequests::route('/'),
            'create' => Pages\CreateRateRequest::route('/create'),
            'edit' => Pages\EditRateRequest::route('/{record}/edit'),
        ];
    }
}
