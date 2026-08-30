<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuotationSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Quotation / Invoice Settings';

    protected static ?string $modelLabel = 'Quotation / invoice setting';

    protected static ?string $pluralModelLabel = 'Quotation / invoice settings';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Quotation / invoice defaults')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('quotation_payment_details')
                        ->rows(5)
                        ->columnSpanFull()
                        ->helperText('This appears on every quotation unless a specific quotation overrides it.'),
                    Forms\Components\Textarea::make('quotation_default_notes')
                        ->rows(4)
                        ->columnSpanFull()
                        ->helperText('Short customer-facing notes for quotations. Keep this concise.'),
                    Forms\Components\Textarea::make('quotation_terms')
                        ->rows(8)
                        ->columnSpanFull()
                        ->helperText('One line per term works well. These terms appear on the quotation document.'),
                    Forms\Components\Repeater::make('quotation_tax_settings')
                        ->label('Taxes & fees')
                        ->columnSpanFull()
                        ->default([
                            [
                                'name' => 'Service Charge',
                                'type' => 'percentage_of_subtotal',
                                'rate_default' => 10,
                                'rate_guesthouse' => 10,
                                'active' => true,
                            ],
                            [
                                'name' => 'TGST',
                                'type' => 'percentage_of_subtotal',
                                'rate_default' => 17,
                                'rate_guesthouse' => 17,
                                'active' => true,
                            ],
                            [
                                'name' => 'Green Tax',
                                'type' => 'per_person_per_night',
                                'rate_default' => 12,
                                'rate_guesthouse' => 6,
                                'active' => true,
                            ],
                        ])
                        ->schema([
                            Forms\Components\TextInput::make('name')->required()->columnSpan(3),
                            Forms\Components\Select::make('type')
                                ->options([
                                    'percentage_of_subtotal' => 'Percentage of subtotal',
                                    'per_person_per_night' => 'Per person per night',
                                    'per_person_once' => 'Per person once',
                                    'fixed' => 'Fixed amount',
                                ])
                                ->required()
                                ->native(false)
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('rate_default')
                                ->numeric()
                                ->required()
                                ->label('Rate: resorts / liveaboards / packages')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('rate_guesthouse')
                                ->numeric()
                                ->required()
                                ->label('Rate: guest houses / city hotels')
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('active')
                                ->default(true)
                                ->columnSpan(2),
                        ])
                        ->columns(12)
                        ->collapsible()
                        ->reorderable(false)
                        ->helperText('These apply automatically to quotations. Staff do not need to add them manually on each quote.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('updated_at')->since()->label('Last updated'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotationSettings::route('/'),
            'create' => Pages\CreateQuotationSetting::route('/create'),
            'edit' => Pages\EditQuotationSetting::route('/{record}/edit'),
        ];
    }
}
