<?php

namespace App\Filament\Widgets;

use App\Models\AgencyPartner;
use App\Models\OperationsTask;
use App\Models\RateRequest;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperationsActivityMetricsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Suppliers contacted this week', Supplier::query()->where('last_contacted_at', '>=', now()->startOfWeek())->count()),
            Stat::make('Agencies contacted this week', AgencyPartner::query()->where('last_contacted_at', '>=', now()->startOfWeek())->count()),
            Stat::make('Rate requests sent this week', RateRequest::query()->where('sent_at', '>=', now()->startOfWeek())->count()),
            Stat::make('Responses received this week', RateRequest::query()->where('response_received_at', '>=', now()->startOfWeek())->count()),
            Stat::make('Rates received this month', RateRequest::query()->where('rates_received', true)->where('response_received_at', '>=', now()->startOfMonth())->count()),
            Stat::make('Active supplier partners', Supplier::query()->where('partnership_status', 'active_partner')->count()),
            Stat::make('Active agency partners', AgencyPartner::query()->where('partnership_status', 'active_partner')->count()),
            Stat::make('Open and overdue tasks', OperationsTask::query()->where('due_at', '<', now())->whereIn('status', ['open', 'in_progress', 'waiting'])->count()),
        ];
    }
}
