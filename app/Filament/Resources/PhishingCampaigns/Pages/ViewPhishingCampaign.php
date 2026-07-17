<?php

namespace App\Filament\Resources\PhishingCampaigns\Pages;

use App\Filament\Resources\PhishingCampaigns\PhishingCampaignResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewPhishingCampaign extends ViewRecord
{
    protected static string $resource = PhishingCampaignResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('name'),
            TextEntry::make('provider'),
            TextEntry::make('status'),
            TextEntry::make('subject'),
            TextEntry::make('from_name'),
            TextEntry::make('from_email'),
            TextEntry::make('reply_to'),
            TextEntry::make('tracking_id'),
            TextEntry::make('total_sent')->label('Sent'),
            TextEntry::make('total_opened')->label('Opened'),
            TextEntry::make('total_clicked')->label('Clicked'),
            TextEntry::make('total_captured')->label('Captured'),
            TextEntry::make('started_at'),
            TextEntry::make('completed_at'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
