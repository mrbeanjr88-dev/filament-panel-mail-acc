<?php

namespace App\Filament\Resources\EmailAccounts\Tables;

use App\Models\EmailAccount;
use App\Services\Imap\EmailSyncService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmailAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('host')->toggleable(),
                IconColumn::make('auto_sync')
                    ->boolean()
                    ->label('Auto-sync')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('pending_emails_count')
                    ->counts('pendingEmails')->label('In quarantine')->badge()->color('warning'),
                TextColumn::make('last_synced_at')->label('Last sync')->since()->sortable(),
                IconColumn::make('has_error')
                    ->state(fn (EmailAccount $r) => filled($r->last_error))
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-check-circle')
                    ->falseColor('success')
                    ->label('Status')
                    ->tooltip(fn (EmailAccount $r) => $r->last_error ?? 'OK'),
            ])
            ->striped()
            ->emptyStateIcon('heroicon-o-at-symbol')
            ->emptyStateHeading('No email accounts')
            ->emptyStateDescription('Add an email account.')
            ->filters([
                TernaryFilter::make('is_active'),
                Filter::make('has_errors')
                    ->query(fn ($q) => $q->whereNotNull('last_error'))
                    ->label('With errors'),
            ])
            ->recordActions([
                Action::make('test')
                    ->label('Test')->icon('heroicon-o-signal')->color('gray')
                    ->action(function (EmailAccount $record) {
                        try {
                            $client = $record->makeClient();
                            $client->getFolder($record->inbox_folder);
                            $client->disconnect();
                            Notification::make()->title('Connection successful')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Connection failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                Action::make('sync')
                    ->label('Sync now')->icon('heroicon-o-arrow-path')->requiresConfirmation()
                    ->action(function (EmailAccount $record) {
                        try {
                            $count = app(EmailSyncService::class)->sync($record);
                            Notification::make()->title("{$count} new emails in quarantine")->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Sync failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
