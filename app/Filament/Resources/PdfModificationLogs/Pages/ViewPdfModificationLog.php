<?php

namespace App\Filament\Resources\PdfModificationLogs\Pages;

use App\Filament\Resources\PdfModificationLogs\PdfModificationLogResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewPdfModificationLog extends ViewRecord
{
    protected static string $resource = PdfModificationLogResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('operation')->label('Operation'),
            TextEntry::make('status')->label('Status'),
            TextEntry::make('pendingEmail.email')->label('Email'),
            TextEntry::make('attachment.filename')->label('Attachment'),
            TextEntry::make('template.name')->label('Template'),
            TextEntry::make('performed_by')->label('Operator'),
            TextEntry::make('error')->label('Error')->color('danger'),
            TextEntry::make('created_at')->label('When'),

            KeyValueEntry::make('params')->label('Parameters'),
            KeyValueEntry::make('result')->label('Result'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
