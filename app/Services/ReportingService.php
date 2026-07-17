<?php

namespace App\Services;

use App\Models\PendingEmail;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportingService
{
    public function getDailyProcessingStats(Carbon $date): array
    {
        $startOfDay = $date->clone()->startOfDay();
        $endOfDay = $date->clone()->endOfDay();

        return [
            'approved_count' => PendingEmail::whereBetween('approved_at', [$startOfDay, $endOfDay])
                ->where('status', 'processed')
                ->count(),
            'rejected_count' => PendingEmail::whereBetween('rejected_at', [$startOfDay, $endOfDay])
                ->where('status', 'rejected')
                ->count(),
            'total_amount_processed' => PendingEmail::whereBetween('approved_at', [$startOfDay, $endOfDay])
                ->sum('extracted_amount'),
            'by_bank' => PendingEmail::whereBetween('approved_at', [$startOfDay, $endOfDay])
                ->with('bankAccount')
                ->where('status', 'processed')
                ->get()
                ->groupBy('bank_account_id')
                ->map(fn ($group) => [
                    'bank' => $group->first()->bankAccount?->label ?? 'Unknown',
                    'count' => $group->count(),
                    'total' => $group->sum('extracted_amount'),
                ]),
            'by_operator' => PendingEmail::whereBetween('approved_at', [$startOfDay, $endOfDay])
                ->where('status', 'processed')
                ->get()
                ->groupBy('approved_by')
                ->map(fn ($group) => [
                    'operator' => $group->first()->approved_by ?? 'System',
                    'count' => $group->count(),
                ]),
        ];
    }

    public function getMonthlyTrends(int $months = 12): Collection
    {
        $now = now();
        $data = collect();

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $now->clone()->subMonths($i)->startOfMonth();
            $endOfMonth = $month->clone()->endOfMonth();

            $approved = PendingEmail::whereBetween('approved_at', [$month, $endOfMonth])
                ->where('status', 'processed')
                ->count();

            $total = PendingEmail::whereBetween('approved_at', [$month, $endOfMonth])
                ->sum('extracted_amount');

            $data->push([
                'month' => $month->format('M Y'),
                'month_key' => $month->format('Y-m'),
                'approved' => $approved,
                'total' => (float) $total,
            ]);
        }

        return $data;
    }

    public function getOperatorStats(int $days = 30): Collection
    {
        $since = now()->subDays($days);

        return PendingEmail::where('approved_at', '>=', $since)
            ->where('status', 'processed')
            ->get()
            ->groupBy('approved_by')
            ->map(fn ($group) => [
                'operator' => $group->first()->approved_by ?? 'System',
                'approved_count' => $group->count(),
                'total_amount' => $group->sum('extracted_amount'),
                'avg_amount' => $group->average('extracted_amount'),
            ])
            ->sortByDesc('approved_count')
            ->values();
    }

    public function getBankAccountStats(int $days = 30): Collection
    {
        $since = now()->subDays($days);

        return PendingEmail::where('approved_at', '>=', $since)
            ->where('status', 'processed')
            ->with('bankAccount')
            ->get()
            ->groupBy('bank_account_id')
            ->map(fn ($group) => [
                'bank_account' => $group->first()->bankAccount?->label ?? 'Unknown',
                'transaction_count' => $group->count(),
                'total_amount' => $group->sum('extracted_amount'),
                'balance' => $group->first()->bankAccount?->current_balance,
            ])
            ->sortByDesc('transaction_count')
            ->values();
    }

    public function getSummary(): array
    {
        return [
            'total_processed' => PendingEmail::where('status', 'processed')->count(),
            'total_pending' => PendingEmail::where('status', 'pending')->count(),
            'total_rejected' => PendingEmail::where('status', 'rejected')->count(),
            'today_approved' => PendingEmail::where('status', 'processed')
                ->whereDate('approved_at', now())
                ->count(),
            'this_month_amount' => PendingEmail::where('status', 'processed')
                ->whereMonth('approved_at', now()->month)
                ->whereYear('approved_at', now()->year)
                ->sum('extracted_amount'),
            'this_month_count' => PendingEmail::where('status', 'processed')
                ->whereMonth('approved_at', now()->month)
                ->whereYear('approved_at', now()->year)
                ->count(),
        ];
    }

    public function getTopCategories(int $limit = 5): Collection
    {
        return PendingEmail::where('status', 'processed')
            ->get()
            ->groupBy('category')
            ->map(fn ($group) => [
                'category' => $group->first()->category?->getLabel() ?? 'Unknown',
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->take($limit)
            ->values();
    }
}
