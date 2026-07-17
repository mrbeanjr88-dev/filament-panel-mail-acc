<?php

namespace App\Filament\Widgets;

use App\Models\PendingEmail;
use App\Models\PdfModificationLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PdfStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $intercepted = PendingEmail::where('pdf_intercepted', true)->count();
        $modified = PendingEmail::where('pdf_modified', true)->count();
        $todayModifications = PdfModificationLog::whereDate('created_at', today())->count();
        $totalOperations = PdfModificationLog::count();

        return [
            Stat::make('Intercepted PDFs', $intercepted)
                ->description('PDF attachments held in quarantine')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Modified PDFs', $modified)
                ->description('PDF attachments modified by operators')
                ->descriptionIcon('heroicon-o-document-check')
                ->color('warning'),

            Stat::make('Modifications today', $todayModifications)
                ->description('PDF operations performed today')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('success'),

            Stat::make('Total PDF operations', $totalOperations)
                ->description('All PDF operations in the system')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('gray'),
        ];
    }
}
