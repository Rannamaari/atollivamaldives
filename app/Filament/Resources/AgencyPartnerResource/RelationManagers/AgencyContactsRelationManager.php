<?php

namespace App\Filament\Resources\AgencyPartnerResource\RelationManagers;

use App\Enums\ContactDepartment;
use App\Enums\ContactMethod;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AgencyContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('full_name')->required(),
            Forms\Components\TextInput::make('position'),
            Forms\Components\Select::make('department')->options(ContactDepartment::options()),
            Forms\Components\TextInput::make('email')->email(),
            Forms\Components\TextInput::make('telephone'),
            Forms\Components\TextInput::make('whatsapp_number'),
            Forms\Components\Select::make('preferred_contact_method')->options(ContactMethod::options()),
            Forms\Components\Toggle::make('is_primary')->default(false),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->searchable(),
                Tables\Columns\TextColumn::make('position'),
                Tables\Columns\TextColumn::make('department')->badge(),
                Tables\Columns\IconColumn::make('is_primary')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
