<?php

namespace App\Filament\Pages;

use App\Services\ReportingService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class EmailReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'Reports';
    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.email-reports';

    protected function getViewData(): array
    {
        $service = app(ReportingService::class);

        return [
            'summary'        => $service->getSummary(),
            'operatorStats'  => $service->getOperatorStats(30),
            'bankStats'      => $service->getBankAccountStats(30),
            'monthlyTrends'  => $service->getMonthlyTrends(12),
            'topCategories'  => $service->getTopCategories(5),
        ];
    }
}
