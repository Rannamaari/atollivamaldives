<?php

namespace App\Filament\Resources\AgencyEmailCampaignResource\Pages;

use App\Enums\AgencyEmailCampaignStatus;
use App\Filament\Resources\AgencyEmailCampaignResource;
use App\Services\OperationsHub\AgencyEmailCampaignService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAgencyEmailCampaign extends EditRecord
{
    protected static string $resource = AgencyEmailCampaignResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function afterSave(): void
    {
        if (in_array($this->record->status, [AgencyEmailCampaignStatus::Draft, AgencyEmailCampaignStatus::Paused, AgencyEmailCampaignStatus::Scheduled], true)) {
            app(AgencyEmailCampaignService::class)->syncRecipientsAndSchedule($this->record->fresh());
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('reschedule')
                ->label('Rebuild schedule')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function (AgencyEmailCampaignService $service): void {
                    $service->syncRecipientsAndSchedule($this->record->fresh());

                    Notification::make()->success()->title('Schedule rebuilt')->body('Recipients and send slots were refreshed.')->send();
                }),
        ];
    }
}
