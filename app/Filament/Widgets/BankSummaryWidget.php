<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BankAccounts\BankAccountResource;
use App\Models\BankAccount;
use App\Services\ReportingService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BankSummaryWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $summary        = app(ReportingService::class)->getSummary();
        $activeAccounts = BankAccount::where('is_active', true)->count();
        $bankUrl        = BankAccountResource::getUrl('index');

        $sumLabel = number_format($summary['this_month_amount'], 2) . ' RON';

        return [
            Stat::make('Transactions this month', $summary['this_month_count'])
                ->description($sumLabel . ' processed')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->url($bankUrl),

            Stat::make('Active bank accounts', $activeAccounts)
                ->description($activeAccounts > 0 ? 'Monitored accounts' : 'No active accounts')
                ->descriptionIcon('heroicon-o-building-library')
                ->color($activeAccounts > 0 ? 'primary' : 'danger')
                ->url($bankUrl),
        ];
    }
}
