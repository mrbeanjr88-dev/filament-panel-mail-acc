<?php

namespace App\Filament\Resources\PendingEmails\Pages;

use App\Enums\PendingEmailStatus;
use App\Filament\Resources\PendingEmails\PendingEmailResource;
use App\Models\EmailAccount;
use App\Models\PendingEmail;
use App\Services\Imap\EmailSyncService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListPendingEmails extends ListRecords
{
    protected static string $resource = PendingEmailResource::class;

    public function getTabs(): array
    {
        return [
            'all'       => Tab::make('All')->badge(PendingEmail::count()),
            'pending'   => Tab::make('Pending')
                ->badge(PendingEmail::where('status', PendingEmailStatus::Pending)->count())
                ->badgeColor('warning'),
            'failed'    => Tab::make('Failed')
                ->badge(PendingEmail::where('status', PendingEmailStatus::Failed)->count())
                ->badgeColor('danger'),
            'processed' => Tab::make('Processed')
                ->badge(PendingEmail::where('status', PendingEmailStatus::Processed)->count())
                ->badgeColor('success'),
            'rejected'  => Tab::make('Rejected')
                ->badge(PendingEmail::where('status', PendingEmailStatus::Rejected)->count())
                ->badgeColor('gray'),
            'trashed'   => Tab::make('Archived')
                ->badge(PendingEmail::onlyTrashed()->count())
                ->badgeColor('gray'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncAll')
                ->label('Sync all accounts')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    $svc = app(EmailSyncService::class);
                    $total = 0;
                    foreach (EmailAccount::where('is_active', true)->get() as $account) {
                        try {
                            $total += $svc->sync($account);
                        } catch (\Throwable $e) {
                            Notification::make()->title("Error {$account->name}")
                                ->body($e->getMessage())->danger()->send();
                        }
                    }
                    Notification::make()->title("{$total} new emails in quarantine")->success()->send();
                }),
        ];
    }
}
