<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Bookings / Sales';

    protected static ?string $navigationLabel = 'Customers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Customer details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('first_name'),
                    Forms\Components\TextInput::make('last_name'),
                    Forms\Components\TextInput::make('company_name'),
                    Forms\Components\TextInput::make('email')->email(),
                    Forms\Components\TextInput::make('phone'),
                    Forms\Components\TextInput::make('whatsapp'),
                    Forms\Components\Select::make('country')->options(array_combine(config('countries.all', []), config('countries.all', [])))->searchable(),
                    Forms\Components\Textarea::make('address')->rows(3)->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Identity details')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('passport_number'),
                    Forms\Components\TextInput::make('work_permit_number'),
                    Forms\Components\TextInput::make('national_id_number')->label('ID card number'),
                ]),
            Forms\Components\Section::make('Dependents')
                ->description('Add spouse, children, or other accompanying dependents here so quotation preparation is easier later.')
                ->schema([
                    Forms\Components\Repeater::make('dependents')
                        ->defaultItems(0)
                        ->schema([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\TextInput::make('relationship')->placeholder('e.g. Child, Spouse'),
                            Forms\Components\TextInput::make('passport_number'),
                            Forms\Components\TextInput::make('work_permit_number'),
                            Forms\Components\TextInput::make('national_id_number')->label('ID card number'),
                            Forms\Components\DatePicker::make('date_of_birth'),
                            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->reorderable(false),
                ]),
            Forms\Components\Section::make('Internal notes')
                ->schema([
                    Forms\Components\Textarea::make('notes')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Customer')->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('passport_number')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('work_permit_number')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('national_id_number')->label('ID card number')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('country')->searchable(),
                Tables\Columns\TextColumn::make('inquiries_count')->counts('inquiries')->label('Inquiries'),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Last contact'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
