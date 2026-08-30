<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Models\Inquiry;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return QuotationResource::mutateQuotationData($data);
    }

    protected function afterCreate(): void
    {
        $inquiry = Inquiry::query()->find($this->record->inquiry_id);

        if ($inquiry && $inquiry->status !== 'quotation_sent') {
            $inquiry->update(['status' => 'quotation_sent']);
        }
    }
}
