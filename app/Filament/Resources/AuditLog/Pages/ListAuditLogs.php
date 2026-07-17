<?php

namespace App\Filament\Resources\AuditLog\Pages;

use App\Filament\Resources\AuditLogResource;
use App\Models\AuditLog;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $rows = AuditLog::with('user')->orderByDesc('timestamp')->get();
                    $csv = "Timestamp,User,Action,Model,ID,IP\n";
                    foreach ($rows as $r) {
                        $csv .= implode(',', [
                            $r->timestamp?->format('d.m.Y H:i:s'),
                            $r->user?->email ?? '—',
                            $r->action,
                            class_basename($r->model_type),
                            $r->model_id ?? '—',
                            $r->ip_address ?? '—',
                        ]) . "\n";
                    }
                    return response()->streamDownload(
                        fn () => print($csv),
                        'audit-log-' . now()->format('Ymd-His') . '.csv',
                        ['Content-Type' => 'text/csv']
                    );
                }),
        ];
    }
}
