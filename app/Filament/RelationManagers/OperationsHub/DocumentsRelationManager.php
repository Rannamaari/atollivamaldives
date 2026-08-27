<?php

namespace App\Filament\RelationManagers\OperationsHub;

use App\Enums\DocumentType;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('document_type')->options(DocumentType::options())->required(),
            Forms\Components\FileUpload::make('stored_path')
                ->label('Document')
                ->required()
                ->disk('local')
                ->directory('operations/documents')
                ->maxSize(20480)
                ->storeFileNamesIn('original_filename')
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]),
            Forms\Components\DatePicker::make('issue_date'),
            Forms\Components\DatePicker::make('effective_date'),
            Forms\Components\DatePicker::make('expiry_date'),
            Forms\Components\Toggle::make('is_confidential')->default(true),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('original_filename')->searchable(),
                Tables\Columns\TextColumn::make('document_type')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? DocumentType::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('expiry_date')->date(),
                Tables\Columns\IconColumn::make('is_confidential')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['stored_filename'] = basename((string) $data['stored_path']);
                        $data['mime_type'] = Storage::disk('local')->mimeType($data['stored_path']) ?: 'application/octet-stream';
                        $data['file_size'] = Storage::disk('local')->size($data['stored_path']) ?: 0;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        return $data;
                    }),
                Tables\Actions\Action::make('download')
                    ->url(fn ($record) => route('operations.documents.download', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
