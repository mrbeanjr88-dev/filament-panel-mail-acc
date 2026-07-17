<?php

namespace App\Filament\Resources\EmailFilterRules\Pages;

use App\Filament\Resources\EmailFilterRules\EmailFilterRuleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailFilterRule extends EditRecord
{
    protected static string $resource = EmailFilterRuleResource::class;

    public function getTitle(): string
    {
        return 'Edit: ' . $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('duplicate')
                ->label('Duplicate rule')
                ->icon('heroicon-o-document-duplicate')
                ->action(function () {
                    $data = $this->record->toArray();
                    unset($data['id'], $data['created_at'], $data['updated_at']);
                    $data['name'] = 'Copy: ' . $data['name'];
                    $data['priority'] = (\App\Models\EmailFilterRule::max('priority') ?? 0) + 10;
                    \App\Models\EmailFilterRule::create($data);
                    \Filament\Notifications\Notification::make()
                        ->title('Rule duplicated')
                        ->success()
                        ->send();
                    $this->redirect(\App\Filament\Resources\EmailFilterRules\EmailFilterRuleResource::getUrl('index'));
                }),
            ...parent::getHeaderActions(),
        ];
    }
}
