<?php

namespace App\Filament\Resources\EmailFilterRules\Pages;

use App\Filament\Resources\EmailFilterRules\EmailFilterRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailFilterRule extends CreateRecord
{
    protected static string $resource = EmailFilterRuleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['priority'] = (\App\Models\EmailFilterRule::max('priority') ?? 0) + 10;
        return $data;
    }
}
