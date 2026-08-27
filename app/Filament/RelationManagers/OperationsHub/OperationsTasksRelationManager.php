<?php

namespace App\Filament\RelationManagers\OperationsHub;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\OperationsTask;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class OperationsTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
            Forms\Components\Textarea::make('description')->columnSpanFull(),
            Forms\Components\Select::make('task_type')->options(TaskType::options())->required(),
            Forms\Components\Select::make('priority')->options(TaskPriority::options())->required(),
            Forms\Components\Select::make('status')->options(TaskStatus::options())->required(),
            Forms\Components\Select::make('assigned_to')->relationship('assignedUser', 'name')->searchable()->preload(),
            Forms\Components\DateTimePicker::make('due_at'),
            Forms\Components\DateTimePicker::make('reminder_at'),
            Forms\Components\Textarea::make('completion_notes')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('priority')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? TaskPriority::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? TaskStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('due_at')->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('complete')
                    ->action(fn (OperationsTask $record) => $record->update(['status' => TaskStatus::Completed, 'completed_at' => now()])),
            ]);
    }
}
