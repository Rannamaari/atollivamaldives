<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Services\OperationsHub\BulkPartnerImporter;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New supplier'),
            Actions\Action::make('downloadTemplate')
                ->label('Download CSV template')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('operations.import-template', ['type' => 'suppliers']), shouldOpenInNewTab: true),
            Actions\Action::make('bulkUpload')
                ->label('Bulk upload')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalHeading('Bulk upload suppliers')
                ->modalDescription('Upload a CSV using the supplier template. Required column: company_name. Supplier contact columns are optional.')
                ->form([
                    Forms\Components\FileUpload::make('csv_file')
                        ->label('CSV file')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->storeFiles(false)
                        ->required()
                        ->helperText('Use the downloaded template so the columns match exactly.'),
                ])
                ->action(function (array $data, BulkPartnerImporter $importer): void {
                    $summary = $importer->importSuppliers($data['csv_file']);

                    Notification::make()
                        ->title('Supplier import finished')
                        ->success()
                        ->body(
                            'Created: '.$summary['created']
                            .' | Updated: '.$summary['updated']
                            .' | Contacts created: '.$summary['contacts_created']
                            .(blank($summary['errors']) ? '' : ' | Errors: '.count($summary['errors']))
                        )
                        ->send();

                    if (filled($summary['errors'])) {
                        Notification::make()
                            ->title('Some rows need attention')
                            ->warning()
                            ->body(implode(' | ', array_slice($summary['errors'], 0, 3)))
                            ->send();
                    }
                }),
        ];
    }
}
