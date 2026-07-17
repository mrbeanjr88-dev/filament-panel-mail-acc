<?php

namespace App\Filament\Resources\PhishingCampaigns\Pages;

use App\Filament\Resources\PhishingCampaigns\PhishingCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPhishingCampaigns extends ListRecords
{
    protected static string $resource = PhishingCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
