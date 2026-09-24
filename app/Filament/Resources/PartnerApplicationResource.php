<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerApplicationResource\Pages;
use App\Models\PartnerApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerApplicationResource extends Resource
{
    protected static ?string $model = PartnerApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Partner Applications';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Application status')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options(PartnerApplication::STATUSES)
                        ->required()
                        ->native(false),
                    Forms\Components\DateTimePicker::make('submitted_at')
                        ->disabled()
                        ->dehydrated(false),
                ])->columns(2),
            Forms\Components\Section::make('Business and contact')
                ->schema([
                    Forms\Components\TextInput::make('company')->required(),
                    Forms\Components\TextInput::make('contact_name')->required(),
                    Forms\Components\TextInput::make('country')->required(),
                    Forms\Components\TextInput::make('email')->email()->required(),
                    Forms\Components\TextInput::make('phone')->required(),
                    Forms\Components\TextInput::make('website')->url(),
                    Forms\Components\Select::make('business_type')
                        ->options(PartnerApplication::BUSINESS_TYPES)
                        ->native(false),
                    Forms\Components\Select::make('estimated_enquiries')
                        ->label('Estimated Maldives enquiries')
                        ->options(PartnerApplication::ENQUIRY_VOLUMES)
                        ->native(false),
                    Forms\Components\Textarea::make('markets')
                        ->label('Main markets / countries served')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('message')
                        ->label('About the business')
                        ->rows(5)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('marketing_opt_in')
                        ->label('Receives B2B offers and partner updates')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->searchPlaceholder('Search company, contact, email, or country')
            ->columns([
                Tables\Columns\TextColumn::make('company')->searchable()->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('contact_name')->label('Contact')->searchable(),
                Tables\Columns\TextColumn::make('country')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('business_type')
                    ->label('Business type')
                    ->formatStateUsing(fn (?string $state): string => PartnerApplication::BUSINESS_TYPES[$state] ?? (string) $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('estimated_enquiries')
                    ->label('Enquiries')
                    ->formatStateUsing(fn (?string $state): string => PartnerApplication::ENQUIRY_VOLUMES[$state] ?? (string) $state)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => PartnerApplication::STATUSES[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'declined' => 'danger',
                        'contacted', 'verified' => 'info',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PartnerApplication::STATUSES),
                Tables\Filters\SelectFilter::make('business_type')->options(PartnerApplication::BUSINESS_TYPES),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerApplications::route('/'),
            'edit' => Pages\EditPartnerApplication::route('/{record}/edit'),
        ];
    }
}
