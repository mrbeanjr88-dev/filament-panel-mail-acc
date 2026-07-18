<?php

namespace App\Filament\Resources\PdfModificationLogs\Pages;

use App\Filament\Resources\PdfModificationLogs\PdfModificationLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPdfModificationLogs extends ListRecords
{
    protected static string $resource = PdfModificationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
