<?php

namespace App\Filament\Resources\PhishingCampaigns\Pages;

use App\Filament\Resources\PhishingCampaigns\PhishingCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPhishingCampaign extends EditRecord
{
    protected static string $resource = PhishingCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
