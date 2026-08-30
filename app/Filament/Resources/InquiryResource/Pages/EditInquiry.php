<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use App\Filament\Resources\QuotationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInquiry extends EditRecord
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('createQuotation')
                ->label('Create quotation')
                ->icon('heroicon-o-document-plus')
                ->url(fn () => QuotationResource::getUrl('create').'?inquiry='.$this->record->id),
            Actions\DeleteAction::make(),
        ];
    }
}
