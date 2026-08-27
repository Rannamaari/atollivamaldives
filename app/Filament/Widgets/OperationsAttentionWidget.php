<?php

namespace App\Filament\Widgets;

use App\Models\AgencyPartner;
use App\Models\DocumentRecord;
use App\Models\OperationsTask;
use App\Models\RateRequest;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperationsAttentionWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Suppliers awaiting response', Supplier::query()->where('partnership_status', 'awaiting_response')->count()),
            Stat::make('Rate requests overdue for follow-up', RateRequest::query()->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now())->whereIn('status', ['sent', 'awaiting_response', 'first_follow_up_due', 'second_follow_up_due'])->count()),
            Stat::make('Supplier rates expiring in 30 days', Supplier::query()->whereBetween('rate_validity_end_date', [today(), today()->addDays(30)])->count()),
            Stat::make('Agreements expiring in 60 days', Supplier::query()->whereBetween('agreement_expiry_date', [today(), today()->addDays(60)])->count()),
            Stat::make('Agencies awaiting verification', AgencyPartner::query()->where('partnership_status', 'verification_required')->count()),
            Stat::make('Documents expiring in 60 days', DocumentRecord::query()->whereBetween('expiry_date', [today(), today()->addDays(60)])->count()),
            Stat::make('Unassigned tasks', OperationsTask::query()->whereNull('assigned_to')->whereIn('status', ['open', 'in_progress', 'waiting'])->count()),
        ];
    }
}
