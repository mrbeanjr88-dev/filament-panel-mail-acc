<?php

namespace App\Filament\Resources\EmailAccounts\Pages;

use App\Filament\Resources\EmailAccounts\EmailAccountResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailAccount extends EditRecord
{
    protected static string $resource = EmailAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test')
                ->label('Test connection')
                ->icon('heroicon-o-signal')
                ->action(function () {
                    try {
                        $client = $this->record->makeClient();
                        $client->getFolder($this->record->inbox_folder);
                        $client->disconnect();
                        \Filament\Notifications\Notification::make()
                            ->title('Connection successful')
                            ->body('IMAP connected successfully to ' . $this->record->host)
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Connection failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            ...parent::getHeaderActions(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->update(['last_error' => null]);
    }
}
