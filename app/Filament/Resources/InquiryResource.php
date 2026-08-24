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
    protected static ?string $model=Inquiry::class;
    protected static ?string $navigationIcon='heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup='Customers';
    protected static ?string $navigationLabel='Travel inquiries';
    public static function form(Form $form):Form{return $form->schema([Forms\Components\Section::make('Traveller')->columns(2)->schema([Forms\Components\TextInput::make('name'),Forms\Components\TextInput::make('phone'),Forms\Components\TextInput::make('email'),Forms\Components\Select::make('travel_type')->options(['resort'=>'Resort','guesthouse'=>'Guesthouse','liveaboard'=>'Liveaboard']),Forms\Components\DatePicker::make('travel_date'),Forms\Components\TextInput::make('travellers')->numeric(),Forms\Components\TextInput::make('budget'),Forms\Components\Select::make('status')->options(['new'=>'New','contacted'=>'Contacted','quoted'=>'Quoted','confirmed'=>'Confirmed','closed'=>'Closed'])->required(),Forms\Components\Textarea::make('message')->columnSpanFull()])]);}
    public static function table(Table $table):Table{return $table->defaultSort('created_at','desc')->columns([Tables\Columns\TextColumn::make('name')->searchable(),Tables\Columns\TextColumn::make('phone')->searchable(),Tables\Columns\TextColumn::make('travel_type')->badge(),Tables\Columns\TextColumn::make('travel_date')->date(),Tables\Columns\TextColumn::make('status')->badge(),Tables\Columns\TextColumn::make('created_at')->since()])->filters([Tables\Filters\SelectFilter::make('status')->options(['new'=>'New','contacted'=>'Contacted','quoted'=>'Quoted','confirmed'=>'Confirmed','closed'=>'Closed'])])->actions([Tables\Actions\EditAction::make()]);}
    public static function getPages():array{return ['index'=>Pages\ListInquiries::route('/'),'edit'=>Pages\EditInquiry::route('/{record}/edit')];}
}
