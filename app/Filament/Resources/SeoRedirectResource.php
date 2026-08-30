<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoRedirectResource\Pages;
use App\Models\SeoRedirect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeoRedirectResource extends Resource
{
    protected static ?string $model = SeoRedirect::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'Redirects';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Redirect')->columns(2)->schema([
                Forms\Components\TextInput::make('source_path')
                    ->required()
                    ->helperText('Example: /stays/old-property-name'),
                Forms\Components\TextInput::make('destination_path')
                    ->required()
                    ->helperText('Example: /travel-products/new-property-name'),
                Forms\Components\Select::make('http_status')
                    ->options([
                        301 => '301 Permanent redirect',
                        302 => '302 Temporary redirect',
                    ])
                    ->default(301)
                    ->required(),
                Forms\Components\Toggle::make('active')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('source_path')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('destination_path')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('http_status')->badge(),
                Tables\Columns\IconColumn::make('active')->boolean(),
                Tables\Columns\TextColumn::make('hits')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeoRedirects::route('/'),
            'create' => Pages\CreateSeoRedirect::route('/create'),
            'edit' => Pages\EditSeoRedirect::route('/{record}/edit'),
        ];
    }
}
