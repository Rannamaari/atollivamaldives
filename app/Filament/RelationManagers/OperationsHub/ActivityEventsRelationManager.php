<?php

namespace App\Filament\RelationManagers\OperationsHub;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'activityEvents';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('event_type')->badge(),
                Tables\Columns\TextColumn::make('title')->wrap()->searchable(),
                Tables\Columns\TextColumn::make('description')->wrap()->limit(80),
                Tables\Columns\TextColumn::make('user.name')->label('By'),
            ])
            ->actions([])
            ->headerActions([]);
    }
}
