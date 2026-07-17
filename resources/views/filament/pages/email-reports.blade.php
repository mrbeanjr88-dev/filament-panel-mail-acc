<x-filament-panels::page>
    <div class="space-y-8">

        {{-- ── KPI Cards ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Processed today --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-5 text-white shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-100">Processed today</p>
                        <p class="mt-2 text-4xl font-extrabold">{{ $summary['today_approved'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white/20 p-2.5">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-xs text-emerald-200">Emails approved today</p>
            </div>

            {{-- Pending --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $summary['total_pending'] > 0 ? 'from-amber-400 to-amber-600' : 'from-gray-400 to-gray-600' }} p-5 text-white shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-amber-100">Pending</p>
                        <p class="mt-2 text-4xl font-extrabold">{{ $summary['total_pending'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white/20 p-2.5">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-xs text-amber-200">{{ $summary['total_pending'] > 0 ? 'Needs review' : 'All clear' }}</p>
            </div>

            {{-- This month --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 to-sky-700 p-5 text-white shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-sky-100">This month</p>
                        <p class="mt-2 text-4xl font-extrabold">{{ $summary['this_month_count'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white/20 p-2.5">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-xs text-sky-200">{{ number_format($summary['this_month_amount'], 2) }} RON processed</p>
            </div>

            {{-- Total rejected --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-rose-700 p-5 text-white shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-rose-100">Total rejected</p>
                        <p class="mt-2 text-4xl font-extrabold">{{ $summary['total_rejected'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white/20 p-2.5">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-xs text-rose-200">Emails rejected by operators</p>
            </div>
        </div>

        {{-- ── Operators & Banks ──────────────────────────────────────── --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- Top Operators --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex-shrink-0 rounded-lg bg-violet-100 dark:bg-violet-900/40 p-2">
                        <svg class="h-4 w-4 text-violet-600 dark:text-violet-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Top Operators</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last 30 days</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/60">
                                <th class="text-left py-2.5 px-6 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operator</th>
                                <th class="text-right py-2.5 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Approved</th>
                                <th class="text-right py-2.5 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total amount</th>
                                <th class="text-right py-2.5 px-6 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Average</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($operatorStats as $i => $stat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="py-3 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 h-7 w-7 rounded-full flex items-center justify-center text-xs font-bold text-white"
                                                 style="background: hsl({{ ($i * 60 + 250) % 360 }}, 60%, 55%)">
                                                {{ strtoupper(substr($stat['operator'], 0, 1)) }}
                                            </div>
                                            <span class="text-gray-900 dark:text-white text-sm">{{ $stat['operator'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-right py-3 px-4 font-medium text-gray-900 dark:text-white">{{ $stat['approved_count'] }}</td>
                                    <td class="text-right py-3 px-4 text-emerald-600 dark:text-emerald-400 font-medium">{{ number_format($stat['total_amount'], 2) }}</td>
                                    <td class="text-right py-3 px-6 text-gray-500 dark:text-gray-400">{{ number_format($stat['avg_amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                        <svg class="mx-auto mb-2 h-8 w-8 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bank Accounts --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex-shrink-0 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 p-2">
                        <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Bank Accounts</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last 30 days</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/60">
                                <th class="text-left py-2.5 px-6 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bank account</th>
                                <th class="text-right py-2.5 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transactions</th>
                                <th class="text-right py-2.5 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total amount</th>
                                <th class="text-right py-2.5 px-6 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($bankStats as $stat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">{{ $stat['bank_account'] }}</td>
                                    <td class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">{{ $stat['transaction_count'] }}</td>
                                    <td class="text-right py-3 px-4 text-emerald-600 dark:text-emerald-400 font-medium">{{ number_format($stat['total_amount'], 2) }}</td>
                                    <td class="text-right py-3 px-6 text-sky-600 dark:text-sky-400 font-semibold">{{ number_format($stat['balance'] ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                        <svg class="mx-auto mb-2 h-8 w-8 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                                        </svg>
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Monthly Trend + Categories ──────────────────────────────── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Monthly Trend --}}
            <div class="xl:col-span-2 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex-shrink-0 rounded-lg bg-sky-100 dark:bg-sky-900/40 p-2">
                        <svg class="h-4 w-4 text-sky-600 dark:text-sky-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Monthly Trend</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last 12 months</p>
                    </div>
                </div>

                @php
                    $maxTotal = collect($monthlyTrends)->max('total') ?: 1;
                    $prevApproved = null;
                @endphp

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/60">
                                <th class="text-left py-2.5 px-6 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                                <th class="text-right py-2.5 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Approved</th>
                                <th class="py-2.5 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Volume</th>
                                <th class="text-right py-2.5 px-6 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($monthlyTrends as $trend)
                                @php
                                    $pct = $maxTotal > 0 ? round(($trend['total'] / $maxTotal) * 100) : 0;
                                    $trend_dir = $prevApproved !== null
                                        ? ($trend['approved'] > $prevApproved ? 'up' : ($trend['approved'] < $prevApproved ? 'down' : 'same'))
                                        : 'same';
                                    $prevApproved = $trend['approved'];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="py-3 px-6">
                                        <div class="flex items-center gap-2">
                                            @if($trend_dir === 'up')
                                                <svg class="h-3.5 w-3.5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" clip-rule="evenodd" />
                                                </svg>
                                            @elseif($trend_dir === 'down')
                                                <svg class="h-3.5 w-3.5 text-rose-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" />
                                                </svg>
                                            @endif
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $trend['month'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-right py-3 px-4 font-medium text-gray-700 dark:text-gray-300">{{ $trend['approved'] }}</td>
                                    <td class="py-3 px-4 w-32">
                                        <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                            <div class="h-2 rounded-full bg-sky-500 dark:bg-sky-400 transition-all" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-right py-3 px-6 text-emerald-600 dark:text-emerald-400 font-semibold">{{ number_format($trend['total'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top Categories --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex-shrink-0 rounded-lg bg-amber-100 dark:bg-amber-900/40 p-2">
                        <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Top Categories</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email distribution</p>
                    </div>
                </div>

                @php
                    $maxCatCount = collect($topCategories)->max('count') ?: 1;
                    $totalCatCount = collect($topCategories)->sum('count') ?: 1;
                    $catColors = ['bg-violet-500', 'bg-sky-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500'];
                @endphp

                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($topCategories as $i => $category)
                        @php
                            $pct = round(($category['count'] / $totalCatCount) * 100);
                            $color = $catColors[$i % count($catColors)];
                        @endphp
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category['category'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $pct }}%</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $category['count'] }}</span>
                                </div>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-1.5 rounded-full {{ $color }} transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                            No data available
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
