<?php

namespace App\Filament\Resources\PhishingCampaigns\Pages;

use App\Filament\Resources\PhishingCampaigns\PhishingCampaignResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewPhishingCampaign extends ViewRecord
{
    protected static string $resource = PhishingCampaignResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Campaign Details')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('provider')->badge(),
                    TextEntry::make('campaign_type')->label('Type')->badge(),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('tracking_id')->label('Tracking ID')->copyable(),
                    TextEntry::make('subject')->columnSpanFull(),
                    TextEntry::make('from_name'),
                    TextEntry::make('from_email'),
                    TextEntry::make('reply_to'),
                ])->columns(3),

            Section::make('Statistics')
                ->schema([
                    TextEntry::make('total_sent')->label('Sent'),
                    TextEntry::make('total_opened')->label('Opened'),
                    TextEntry::make('total_clicked')->label('Clicked'),
                    TextEntry::make('total_captured')->label('Captured'),
                ])->columns(4),

            Section::make('Configuration')
                ->schema([
                    TextEntry::make('deep_link_mode')->label('Deep Link Mode'),
                    TextEntry::make('evilginx_domain')->label('Evilginx Domain'),
                    TextEntry::make('evilginx_phishlet')->label('Evilginx Phishlet'),
                    TextEntry::make('auto_connect_enabled')->label('Auto-Connect')->boolean(),
                    TextEntry::make('started_at'),
                    TextEntry::make('completed_at'),
                ])->columns(3),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
