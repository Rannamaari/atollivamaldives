<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\Quotation;
use App\Models\SiteSetting;
use App\Services\QuotationCalculator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Bookings / Sales';

    protected static ?string $navigationLabel = 'Quotations';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Quotation')
                ->columns(2)
                ->schema([
                    Forms\Components\Hidden::make('accommodation_id'),
                    Forms\Components\Select::make('customer_id')
                        ->label('Customer')
                        ->relationship('customer', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Customer $record): string => $record->full_name ?: ($record->email ?: ('Customer #'.$record->id)))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->default(fn () => static::defaultCustomerId())
                        ->afterStateHydrated(function ($state, callable $set, Get $get): void {
                            if ($state && blank($get('customer_name'))) {
                                static::applyCustomerDefaults((int) $state, $set);
                            }
                        })
                        ->afterStateUpdated(function ($state, callable $set): void {
                            static::applyCustomerDefaults($state ? (int) $state : null, $set);
                            $set('inquiry_id', null);
                        }),
                    Forms\Components\Select::make('inquiry_id')
                        ->label('Related inquiry')
                        ->relationship('inquiry', 'reference')
                        ->getOptionLabelFromRecordUsing(fn (Inquiry $record): string => $record->reference ?: ('Inquiry #'.$record->id))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->helperText('Optional. Select this if the quotation is based on a specific inquiry.')
                        ->options(fn (Get $get) => static::inquiryOptionsForCustomer($get('customer_id')))
                        ->disabled(fn (Get $get) => blank($get('customer_id')))
                        ->afterStateHydrated(function ($state, callable $set): void {
                            if ($state) {
                                static::applyInquiryDefaults((int) $state, $set);
                            }
                        })
                        ->afterStateUpdated(fn ($state, callable $set) => static::applyInquiryDefaults($state ? (int) $state : null, $set)),
                    Forms\Components\TextInput::make('quotation_number')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Auto-generated when saved'),
                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'sent' => 'Sent',
                            'accepted' => 'Accepted',
                            'expired' => 'Expired',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false),
                    Forms\Components\TextInput::make('reference')
                        ->default(fn (Get $get) => static::inquiryReference($get('inquiry_id'))),
                    Forms\Components\DatePicker::make('quotation_date')
                        ->default(now())
                        ->required(),
                    Forms\Components\DatePicker::make('valid_until')
                        ->default(now()->addDays(7)),
                    Forms\Components\TextInput::make('currency')
                        ->default('USD')
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->placeholder('e.g. Maldives stay quotation'),
                    Forms\Components\TextInput::make('property_name')
                        ->label('Property / trip name'),
                    Forms\Components\DatePicker::make('check_in')
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set) => static::syncStayDetails($get, $set)),
                    Forms\Components\DatePicker::make('check_out')
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set) => static::syncStayDetails($get, $set)),
                    Forms\Components\TextInput::make('nights')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set) => static::syncPrimaryLineItem($get, $set, $get('nightly_rate'))),
                    Forms\Components\TextInput::make('nightly_rate')
                        ->numeric()
                        ->prefix('USD')
                        ->dehydrated(false)
                        ->default(fn (Get $get) => (float) data_get($get('items') ?? [], '0.unit_price', 0))
                        ->helperText('Enter the selling rate per night. The first line item will use this with the number of nights.')
                        ->live()
                        ->afterStateUpdated(fn ($state, Get $get, Set $set) => static::syncPrimaryLineItem($get, $set, $state)),
                    Forms\Components\TextInput::make('adults')
                        ->numeric()
                        ->default(2)
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('children')
                        ->numeric()
                        ->default(0)
                        ->live(),
                    Forms\Components\TextInput::make('infants')
                        ->numeric()
                        ->default(0)
                        ->live()
                        ->helperText('Infants under 2 are excluded from green tax by default.'),
                    Forms\Components\TextInput::make('chargeable_pax')
                        ->numeric()
                        ->default(2)
                        ->live()
                        ->helperText('Defaults to adults + children, but you can override it if needed.'),
                    Forms\Components\Textarea::make('itinerary')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder("Day 1 - Arrival and transfer\nDay 2 - Stay / excursions\nDay 3 - Departure")
                        ->helperText('Optional short itinerary or inclusion summary shown on the quotation.'),
                ]),
            Forms\Components\Section::make('Bill to')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('customer_name')->required(),
                    Forms\Components\TextInput::make('company_name'),
                    Forms\Components\Textarea::make('customer_address')->rows(3),
                    Forms\Components\TextInput::make('customer_phone'),
                    Forms\Components\TextInput::make('customer_email')->email(),
                ]),
            Forms\Components\Section::make('Line items')
                ->description('Add the room, package, transfer, or service lines you want to quote.')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->default(fn (Get $get) => static::defaultItems($get('inquiry_id')))
                        ->schema([
                            Forms\Components\TextInput::make('description')->required()->columnSpan(6),
                            Forms\Components\TextInput::make('qty')->numeric()->default(1)->required()->columnSpan(2),
                            Forms\Components\TextInput::make('unit_price')->numeric()->default(0)->required()->prefix('USD')->columnSpan(2),
                            Forms\Components\Placeholder::make('amount_preview')
                                ->label('Amount')
                                ->content(fn (Get $get) => 'USD '.number_format(((float) ($get('qty') ?? 1)) * ((float) ($get('unit_price') ?? 0)), 2))
                                ->columnSpan(2),
                        ])
                        ->columns(12)
                        ->defaultItems(1)
                        ->collapsible()
                        ->reorderable(false),
                ]),
            Forms\Components\Section::make('Managed defaults')
                ->description('Taxes, bank details, and standard quotation wording are managed centrally from Quotation / Invoice Settings.')
                ->schema([
                    Forms\Components\Placeholder::make('managed_defaults_summary')
                        ->label('Active tax rules')
                        ->content(fn (Get $get) => static::renderManagedDefaultsSummary($get)),
                ]),
            Forms\Components\Section::make('Payment details & notes')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('payment_notes')
                        ->rows(5)
                        ->default(fn () => SiteSetting::current()->quotation_payment_details)
                        ->placeholder("Bank Name:\nAccount Name:\nAccount No.:\nSWIFT/BIC:"),
                    Forms\Components\Textarea::make('notes')
                        ->rows(5)
                        ->default(fn () => SiteSetting::current()->quotation_default_notes)
                        ->placeholder("Quotation valid for 7 days.\nRates subject to availability until confirmed."),
                    Forms\Components\TextInput::make('signature_name')->default('Atolliva Maldives'),
                    Forms\Components\TextInput::make('signature_title')->default('Authorized Signature'),
                ]),
            Forms\Components\Section::make('Totals preview')
                ->schema([
                    Forms\Components\Placeholder::make('totals_preview')
                        ->label('')
                        ->content(fn (Get $get) => static::renderTotalsPreview($get)),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('quotation_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->searchable(),
                Tables\Columns\TextColumn::make('property_name')->label('Property / trip')->searchable(),
                Tables\Columns\TextColumn::make('inquiry.reference')->label('Inquiry')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('grand_total')->money('USD')->label('Total'),
                Tables\Columns\TextColumn::make('quotation_date')->date(),
                Tables\Columns\TextColumn::make('valid_until')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('print')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Quotation $record) => route('quotations.print', $record), shouldOpenInNewTab: true),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }

    public static function mutateQuotationData(array $data): array
    {
        $inquiry = filled($data['inquiry_id'] ?? null)
            ? Inquiry::query()->with(['customer', 'accommodation'])->find($data['inquiry_id'])
            : null;

        $customer = filled($data['customer_id'] ?? null)
            ? Customer::query()->find($data['customer_id'])
            : null;

        if ($inquiry) {
            $data['customer_id'] = $inquiry->customer_id;
            $data['accommodation_id'] = $inquiry->accommodation_id;
            $data['reference'] = $data['reference'] ?: $inquiry->reference;
            $data['property_name'] = $data['property_name'] ?: ($inquiry->accommodation?->name ?: static::travelTypeLabel($inquiry->travel_type).' quotation');
            $data['customer_name'] = $data['customer_name'] ?: ($inquiry->customer?->full_name ?: $inquiry->name);
            $data['company_name'] = $data['company_name'] ?: $inquiry->customer?->company_name;
            $data['customer_address'] = $data['customer_address'] ?: $inquiry->customer?->address;
            $data['customer_phone'] = $data['customer_phone'] ?: ($inquiry->customer?->phone ?: $inquiry->phone);
            $data['customer_email'] = $data['customer_email'] ?: ($inquiry->customer?->email ?: $inquiry->email);
        } elseif ($customer) {
            $data['customer_name'] = $data['customer_name'] ?: $customer->full_name;
            $data['company_name'] = $data['company_name'] ?: $customer->company_name;
            $data['customer_address'] = $data['customer_address'] ?: $customer->address;
            $data['customer_phone'] = $data['customer_phone'] ?: ($customer->phone ?: $customer->whatsapp);
            $data['customer_email'] = $data['customer_email'] ?: $customer->email;
        }

        $data['taxes'] = static::defaultTaxes($data['inquiry_id'] ?? null);

        return app(QuotationCalculator::class)->prepare($data);
    }

    public static function applyInquiryDefaults(?int $inquiryId, callable $set): void
    {
        $inquiry = $inquiryId ? Inquiry::query()->with(['customer', 'accommodation'])->find($inquiryId) : null;

        if (! $inquiry) {
            return;
        }

        $set('customer_id', $inquiry->customer_id);
        $set('accommodation_id', $inquiry->accommodation_id);
        $set('reference', $inquiry->reference);
        $set('customer_name', $inquiry->customer?->full_name ?: ($inquiry->name ?: trim(($inquiry->first_name ?? '').' '.($inquiry->last_name ?? ''))));
        $set('company_name', $inquiry->customer?->company_name);
        $set('customer_address', $inquiry->customer?->address);
        $set('customer_phone', $inquiry->customer?->phone ?: $inquiry->phone);
        $set('customer_email', $inquiry->customer?->email ?: $inquiry->email);
        $set('property_name', $inquiry->accommodation?->name ?: static::travelTypeLabel($inquiry->travel_type).' quotation');
        $set('check_in', optional($inquiry->check_in)->format('Y-m-d'));
        $set('check_out', optional($inquiry->check_out)->format('Y-m-d'));
        $set('nights', $inquiry->number_of_nights ?: 1);
        $set('nightly_rate', null);
        $set('adults', $inquiry->adults ?: max(1, (int) ($inquiry->travellers ?: 2)));
        $set('children', $inquiry->children ?: 0);
        $set('infants', $inquiry->infants ?: 0);
        $set('chargeable_pax', max(1, (int) (($inquiry->adults ?: $inquiry->travellers ?: 2) + ($inquiry->children ?: 0))));
        $set('items', static::defaultItems($inquiry->id));
        $set('taxes', static::defaultTaxes($inquiry->id));
        $set('payment_notes', SiteSetting::current()->quotation_payment_details);
        $set('notes', SiteSetting::current()->quotation_default_notes);
    }

    public static function applyCustomerDefaults(?int $customerId, callable $set): void
    {
        $customer = $customerId ? Customer::query()->find($customerId) : null;

        if (! $customer) {
            return;
        }

        $set('customer_name', $customer->fullName ?: '');
        $set('company_name', $customer->company_name);
        $set('customer_address', $customer->address);
        $set('customer_phone', $customer->phone ?: $customer->whatsapp);
        $set('customer_email', $customer->email);
    }

    protected static function defaultItems(?int $inquiryId): array
    {
        $inquiry = $inquiryId ? Inquiry::query()->with('accommodation')->find($inquiryId) : null;

        if (! $inquiry) {
            return [[
                'description' => 'Accommodation / travel service',
                'qty' => 1,
                'unit_price' => 0,
            ]];
        }

        $description = $inquiry->accommodation?->name
            ? $inquiry->accommodation->name.' stay'
            : static::travelTypeLabel($inquiry->travel_type).' arrangement';

        if ($inquiry->number_of_nights) {
            $description .= ' - '.$inquiry->number_of_nights.' night'.($inquiry->number_of_nights === 1 ? '' : 's');
        }

        return [[
            'description' => $description,
            'qty' => max(1, (int) ($inquiry->number_of_nights ?: 1)),
            'unit_price' => 0,
        ]];
    }

    protected static function defaultTaxes(?int $inquiryId): array
    {
        $inquiry = $inquiryId ? Inquiry::query()->find($inquiryId) : null;

        return app(QuotationCalculator::class)->defaultTaxes($inquiry?->travel_type);
    }

    protected static function inquiryReference(?int $inquiryId): ?string
    {
        return $inquiryId ? Inquiry::query()->whereKey($inquiryId)->value('reference') : null;
    }

    protected static function defaultCustomerId(): ?int
    {
        $inquiryId = request()->integer('inquiry');

        return $inquiryId
            ? Inquiry::query()->whereKey($inquiryId)->value('customer_id')
            : null;
    }

    protected static function inquiryOptionsForCustomer(?int $customerId): array
    {
        if (! $customerId) {
            return [];
        }

        return Inquiry::query()
            ->where('customer_id', $customerId)
            ->latest('created_at')
            ->get()
            ->mapWithKeys(fn (Inquiry $inquiry) => [
                $inquiry->id => ($inquiry->reference ?: ('Inquiry #'.$inquiry->id)).' - '.($inquiry->property_name ?? $inquiry->name ?? ucfirst((string) $inquiry->travel_type)),
            ])
            ->all();
    }

    protected static function renderTotalsPreview(Get $get): HtmlString
    {
        $data = app(QuotationCalculator::class)->prepare([
            'items' => $get('items') ?? [],
            'taxes' => static::defaultTaxes($get('inquiry_id')),
            'nights' => $get('nights'),
            'adults' => $get('adults'),
            'children' => $get('children'),
            'infants' => $get('infants'),
            'chargeable_pax' => $get('chargeable_pax'),
        ]);
        $lines = collect($data['taxes'] ?? [])
            ->map(fn (array $tax): string => sprintf('<div class="flex justify-between gap-6"><span>%s</span><span>USD %s</span></div>', e($tax['name']), number_format((float) $tax['total'], 2)))
            ->implode('');

        return new HtmlString(sprintf(
            '<div class="space-y-2 text-sm text-gray-700"><div class="flex justify-between gap-6 font-medium"><span>Subtotal</span><span>USD %s</span></div>%s<div class="flex justify-between gap-6 border-t border-gray-200 pt-2 text-base font-semibold text-gray-900"><span>Total</span><span>USD %s</span></div></div>',
            number_format((float) ($data['subtotal'] ?? 0), 2),
            $lines,
            number_format((float) ($data['grand_total'] ?? 0), 2)
        ));
    }

    protected static function renderManagedDefaultsSummary(Get $get): HtmlString
    {
        $taxes = static::defaultTaxes($get('inquiry_id'));

        $items = collect($taxes)
            ->map(function (array $tax): string {
                $suffix = match ($tax['type'] ?? 'fixed') {
                    'percentage_of_subtotal' => '% of subtotal',
                    'per_person_per_night' => 'per person per night',
                    'per_person_once' => 'per person once',
                    default => 'fixed amount',
                };

                return sprintf('<li><strong>%s</strong>: %s %s</li>', e($tax['name']), e((string) $tax['rate']), e($suffix));
            })
            ->implode('');

        return new HtmlString('<div class="text-sm text-gray-700"><p class="mb-2">These taxes and fees will be applied automatically on save and on the printed quotation.</p><ul class="list-disc space-y-1 pl-5">'.$items.'</ul></div>');
    }

    protected static function syncStayDetails(Get $get, Set $set): void
    {
        $checkIn = $get('check_in');
        $checkOut = $get('check_out');

        if (blank($checkIn) || blank($checkOut)) {
            return;
        }

        $start = \Carbon\Carbon::parse($checkIn);
        $end = \Carbon\Carbon::parse($checkOut);
        $nights = max(1, (int) $start->diffInDays($end));

        $set('nights', $nights);
        static::syncPrimaryLineItem($get, $set, $get('nightly_rate'));
    }

    protected static function syncPrimaryLineItem(Get $get, Set $set, mixed $nightlyRate): void
    {
        $items = $get('items') ?? [];

        if (! is_array($items) || blank($items)) {
            $items = [[
                'description' => 'Accommodation / travel service',
                'qty' => max(1, (int) ($get('nights') ?: 1)),
                'unit_price' => (float) ($nightlyRate ?: 0),
            ]];
        } else {
            $items[0]['qty'] = max(1, (int) ($get('nights') ?: 1));

            if ($nightlyRate !== null && $nightlyRate !== '') {
                $items[0]['unit_price'] = (float) $nightlyRate;
            }
        }

        $set('items', $items);
    }

    protected static function travelTypeLabel(?string $travelType): string
    {
        return match ($travelType) {
            'guesthouse' => 'Guest house',
            'liveaboard' => 'Liveaboard',
            'city_hotel' => 'City hotel',
            'package' => 'Package',
            default => 'Resort',
        };
    }
}
