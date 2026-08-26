<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Inquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Bookings / Sales';

    protected static ?string $navigationLabel = 'Travel inquiries';

    protected static function statusOptions(): array
    {
        return ['new' => 'New', 'contacted' => 'Contacted', 'quotation_sent' => 'Quotation Sent', 'follow_up' => 'Follow Up', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled', 'lost' => 'Lost'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Inquiry')->columns(2)->schema([
                Forms\Components\TextInput::make('reference')->disabled()->dehydrated(false),
                Forms\Components\Select::make('status')->options(static::statusOptions())->required(),
                Forms\Components\Select::make('customer_id')->relationship('customer', 'first_name')->getOptionLabelFromRecordUsing(fn ($record) => trim($record->first_name.' '.$record->last_name))->searchable()->preload(),
                Forms\Components\Select::make('assigned_to')->relationship('assignedUser', 'name')->searchable()->preload(),
                Forms\Components\Select::make('accommodation_id')->relationship('accommodation', 'name')->searchable()->preload()->label('Property'),
                Forms\Components\Select::make('room_id')->relationship('room', 'name')->searchable()->preload(),
                Forms\Components\Select::make('travel_type')->options(['resort' => 'Resort', 'guesthouse' => 'Guest Houses', 'liveaboard' => 'Liveaboards', 'city_hotel' => 'City Hotels', 'package' => 'Packages']),
                Forms\Components\Select::make('nationality')->options(array_combine(config('countries.all', []), config('countries.all', [])))->searchable(),
                Forms\Components\TextInput::make('preferred_atoll')->label('Preferred location / airport distance'),
                Forms\Components\TextInput::make('transfer_preference'),
                Forms\Components\DatePicker::make('check_in'),
                Forms\Components\DatePicker::make('check_out')->afterOrEqual('check_in'),
                Forms\Components\TextInput::make('number_of_nights')->numeric(),
                Forms\Components\TextInput::make('budget'),
                Forms\Components\TextInput::make('adults')->numeric(),
                Forms\Components\TextInput::make('children')->numeric(),
                Forms\Components\TextInput::make('children_ages'),
                Forms\Components\TextInput::make('infants')->numeric(),
                Forms\Components\TextInput::make('preferred_room'),
                Forms\Components\TextInput::make('meal_plan'),
                Forms\Components\Toggle::make('honeymoon'),
                Forms\Components\Toggle::make('family_trip'),
                Forms\Components\Toggle::make('diving_trip'),
                Forms\Components\Toggle::make('surfing_trip'),
                Forms\Components\Textarea::make('message')->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Contact details')->columns(2)->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('first_name'),
                Forms\Components\TextInput::make('last_name'),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\TextInput::make('email'),
                Forms\Components\TextInput::make('source'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([Tables\Columns\TextColumn::make('reference')->searchable(), Tables\Columns\TextColumn::make('name')->searchable(), Tables\Columns\TextColumn::make('accommodation.name')->label('Property')->searchable(), Tables\Columns\TextColumn::make('travel_type')->badge(), Tables\Columns\TextColumn::make('check_in')->date(), Tables\Columns\TextColumn::make('check_out')->date(), Tables\Columns\TextColumn::make('adults'), Tables\Columns\TextColumn::make('children'), Tables\Columns\TextColumn::make('status')->badge(), Tables\Columns\TextColumn::make('created_at')->since()])->filters([Tables\Filters\SelectFilter::make('status')->options(static::statusOptions())])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListInquiries::route('/'), 'edit' => Pages\EditInquiry::route('/{record}/edit')];
    }
}
