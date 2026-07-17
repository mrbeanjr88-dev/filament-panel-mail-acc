<?php

namespace App\Filament\Resources\PdfTemplates\Pages;

use App\Filament\Resources\PdfTemplates\PdfTemplateResource;
use Filament\Resources\Pages\EditRecord;

class EditPdfTemplate extends EditRecord
{
    protected static string $resource = PdfTemplateResource::class;
}
