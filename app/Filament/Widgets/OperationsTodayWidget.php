<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AgencyPartnerResource;
use App\Filament\Resources\OperationsTaskResource;
use App\Filament\Resources\RateRequestResource;
use App\Filament\Resources\SupplierResource;
use App\Models\Communication;
use App\Models\OperationsTask;
use App\Models\RateRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperationsTodayWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $today = today();

        return [
            Stat::make('My tasks due today', OperationsTask::query()->whereDate('due_at', $today)->where('assigned_to', auth()->id())->whereIn('status', ['open', 'in_progress', 'waiting'])->count())
                ->url(OperationsTaskResource::getUrl('index')),
            Stat::make('Overdue tasks', OperationsTask::query()->where('due_at', '<', now())->whereIn('status', ['open', 'in_progress', 'waiting'])->count())
                ->url(OperationsTaskResource::getUrl('index')),
            Stat::make('Supplier follow-ups due', OperationsTask::query()->whereDate('due_at', '<=', $today)->where('task_type', 'supplier_follow_up')->whereIn('status', ['open', 'in_progress', 'waiting'])->count())
                ->url(SupplierResource::getUrl('index')),
            Stat::make('Agency follow-ups due', OperationsTask::query()->whereDate('due_at', '<=', $today)->where('task_type', 'agency_follow_up')->whereIn('status', ['open', 'in_progress', 'waiting'])->count())
                ->url(AgencyPartnerResource::getUrl('index')),
            Stat::make('Rate requests awaiting response', RateRequest::query()->whereIn('status', ['sent', 'awaiting_response', 'first_follow_up_due', 'second_follow_up_due'])->count())
                ->url(RateRequestResource::getUrl('index')),
            Stat::make('Recently added communications', Communication::query()->where('created_at', '>=', now()->subDays(7))->count()),
        ];
    }
}
