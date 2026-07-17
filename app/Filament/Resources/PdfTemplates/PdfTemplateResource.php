<?php

namespace App\Filament\Resources\PdfTemplates;

use App\Filament\Resources\PdfTemplates\Pages\CreatePdfTemplate;
use App\Filament\Resources\PdfTemplates\Pages\EditPdfTemplate;
use App\Filament\Resources\PdfTemplates\Pages\ListPdfTemplates;
use App\Models\PdfTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PdfTemplateResource extends Resource
{
    protected static ?string $model = PdfTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|UnitEnum|null $navigationGroup = 'Email Customs';
    protected static ?string $navigationLabel = 'PDF Templates';
    protected static ?string $modelLabel = 'PDF Template';
    protected static ?string $pluralModelLabel = 'PDF Templates';
    protected static ?int $navigationSort = 5;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

    public static function form(Schema $schema): Schema
    {
        return PdfTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PdfTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPdfTemplates::route('/'),
            'create' => CreatePdfTemplate::route('/create'),
            'edit'   => EditPdfTemplate::route('/{record}/edit'),
        ];
    }
}
