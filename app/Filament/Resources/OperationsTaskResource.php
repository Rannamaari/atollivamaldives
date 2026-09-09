<?php

namespace App\Filament\Resources;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Filament\Resources\OperationsTaskResource\Pages;
use App\Models\OperationsTask;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OperationsTaskResource extends Resource
{
    protected static ?string $model = OperationsTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Task')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                    Forms\Components\Textarea::make('description')->columnSpanFull(),
                    Forms\Components\Select::make('task_type')->options(TaskType::options())->required(),
                    Forms\Components\Select::make('priority')->options(TaskPriority::options())->required(),
                    Forms\Components\Select::make('status')->options(TaskStatus::options())->required(),
                    Forms\Components\Select::make('supplier_id')->relationship('supplier', 'legal_name')->searchable()->preload(),
                    Forms\Components\Select::make('agency_partner_id')->relationship('agencyPartner', 'legal_company_name')->searchable()->preload(),
                    Forms\Components\Select::make('rate_request_id')->relationship('rateRequest', 'request_title')->searchable()->preload(),
                    Forms\Components\Select::make('communication_id')->relationship('communication', 'subject')->searchable()->preload(),
                    Forms\Components\Select::make('assigned_to')->relationship('assignedUser', 'name')->searchable()->preload(),
                    Forms\Components\DateTimePicker::make('due_at'),
                    Forms\Components\DateTimePicker::make('reminder_at'),
                    Forms\Components\DateTimePicker::make('completed_at'),
                    Forms\Components\Textarea::make('completion_notes')->columnSpanFull(),
                    Forms\Components\DateTimePicker::make('snoozed_at'),
                    Forms\Components\TextInput::make('cancellation_reason'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'supplier:id,legal_name',
                'agencyPartner:id,legal_company_name',
                'rateRequest:id,request_title',
                'communication:id,subject,recipient',
                'assignedUser:id,name',
            ]))
            ->defaultSort('due_at')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Task')
                    ->description(fn (OperationsTask $record): ?string => filled($record->description) ? $record->description : null)
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('task_type')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? TaskType::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('related_record')
                    ->label('About')
                    ->getStateUsing(function (OperationsTask $record): string {
                        if ($record->rateRequest) {
                            return 'Rate request: '.$record->rateRequest->request_title;
                        }

                        if ($record->supplier) {
                            return 'Supplier: '.$record->supplier->legal_name;
                        }

                        if ($record->agencyPartner) {
                            return 'Agency: '.$record->agencyPartner->legal_company_name;
                        }

                        if ($record->communication) {
                            return 'Email: '.($record->communication->subject ?: $record->communication->recipient);
                        }

                        return 'General operations task';
                    })
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('priority')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? TaskPriority::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? TaskStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('assignedUser.name')->label('Assigned'),
                Tables\Columns\TextColumn::make('due_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('my_tasks')->queries(
                    true: fn ($query) => $query->where('assigned_to', auth()->id()),
                    false: fn ($query) => $query->where('assigned_to', '!=', auth()->id()),
                    blank: fn ($query) => $query
                ),
                Tables\Filters\Filter::make('due_today')->query(fn ($query) => $query->whereDate('due_at', today())),
                Tables\Filters\Filter::make('overdue')->query(fn ($query) => $query->where('due_at', '<', now())->whereIn('status', ['open', 'in_progress', 'waiting'])),
                Tables\Filters\SelectFilter::make('priority')->options(TaskPriority::options()),
                Tables\Filters\SelectFilter::make('assigned_to')->relationship('assignedUser', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('complete')
                    ->label('Complete')
                    ->requiresConfirmation()
                    ->action(fn (OperationsTask $record) => $record->update(['status' => TaskStatus::Completed, 'completed_at' => now()])),
                Action::make('snooze')
                    ->form([Forms\Components\DateTimePicker::make('due_at')->required()])
                    ->action(function (OperationsTask $record, array $data): void {
                        $record->update([
                            'due_at' => $data['due_at'],
                            'snoozed_at' => now(),
                            'status' => TaskStatus::Waiting,
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOperationsTasks::route('/'),
            'create' => Pages\CreateOperationsTask::route('/create'),
            'edit' => Pages\EditOperationsTask::route('/{record}/edit'),
        ];
    }
}
