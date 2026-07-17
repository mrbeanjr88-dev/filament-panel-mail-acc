<?php

namespace App\Filament\Widgets;

use App\Enums\PendingEmailStatus;
use App\Filament\Resources\PendingEmails\PendingEmailResource;
use App\Models\PendingEmail;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class QuarantineStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $pending  = PendingEmail::where('status', PendingEmailStatus::Pending)->count();
        $approved = PendingEmail::where('status', PendingEmailStatus::Processed)
            ->whereDate('approved_at', today())->count();
        $failed   = PendingEmail::where('status', PendingEmailStatus::Failed)->count();
        $rejected = PendingEmail::where('status', PendingEmailStatus::Rejected)->count();

        // Chart sparklines for last 7 days
        $pendingChart  = $this->last7DaysChart(PendingEmailStatus::Pending, 'created_at');
        $approvedChart = $this->last7DaysChart(PendingEmailStatus::Processed, 'approved_at');
        $failedChart   = $this->last7DaysChart(PendingEmailStatus::Failed, 'updated_at');
        $rejectedChart = $this->last7DaysChart(PendingEmailStatus::Rejected, 'updated_at');

        $indexUrl = PendingEmailResource::getUrl('index');

        return [
            Stat::make('In Quarantine', $pending)
                ->description($pending > 0 ? 'Needs attention' : 'No action needed')
                ->descriptionIcon($pending > 0 ? 'heroicon-o-clock' : 'heroicon-o-check-circle')
                ->color($pending > 0 ? 'warning' : 'success')
                ->chart($pendingChart)
                ->url($indexUrl),

            Stat::make('Approved today', $approved)
                ->description('Processed and moved to Inbox')
                ->descriptionIcon('heroicon-o-inbox-arrow-down')
                ->color('success')
                ->chart($approvedChart)
                ->url($indexUrl),

            Stat::make('Failed', $failed)
                ->description($failed > 0 ? 'Check IMAP errors' : 'No errors')
                ->descriptionIcon($failed > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->color($failed > 0 ? 'danger' : 'success')
                ->chart($failedChart)
                ->url($indexUrl),

            Stat::make('Total Rejected', $rejected)
                ->description('Emails rejected by operators')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($rejected > 0 ? 'gray' : 'success')
                ->chart($rejectedChart)
                ->url($indexUrl),
        ];
    }

    private function last7DaysChart(PendingEmailStatus $status, string $dateColumn): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i)->toDateString());

        $counts = PendingEmail::where('status', $status)
            ->where($dateColumn, '>=', Carbon::today()->subDays(6))
            ->selectRaw("date($dateColumn) as day, count(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        return $days->map(fn ($d) => (int) ($counts[$d] ?? 0))->values()->all();
    }
}
