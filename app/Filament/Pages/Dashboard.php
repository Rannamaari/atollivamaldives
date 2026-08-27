<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OperationsActivityMetricsWidget;
use App\Filament\Widgets\OperationsAttentionWidget;
use App\Filament\Widgets\OperationsTodayWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Operations Dashboard';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return 'Operations Dashboard';
    }

    public function getWidgets(): array
    {
        return [
            OperationsTodayWidget::class,
            OperationsAttentionWidget::class,
            OperationsActivityMetricsWidget::class,
        ];
    }
}
