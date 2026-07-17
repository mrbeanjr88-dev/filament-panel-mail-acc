<?php

namespace App\Filament\Resources\PdfTemplates\Tables;

use App\Models\PdfTemplate;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PdfTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('priority', 'asc')
            ->striped()
            ->columns([
                TextColumn::make('priority')->label('P')->sortable()->width(40),
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('type')->label('Type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'watermark'     => 'info',
                        'stamp'         => 'success',
                        'header_footer' => 'primary',
                        'merge'         => 'warning',
                        'redact'        => 'danger',
                        'extract'       => 'gray',
                        'rotate'        => 'gray',
                        'flatten'       => 'gray',
                        'generate'      => 'info',
                        default         => 'gray',
                    }),
                TextColumn::make('emailAccount.name')->label('Account')->placeholder('All'),
                IconColumn::make('auto_apply')->label('Auto')->boolean(),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('created_at')->label('Created')->dateTime('d.m.Y'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'watermark'     => 'Watermark',
                        'stamp'         => 'Stamp',
                        'header_footer' => 'Header/Footer',
                        'merge'         => 'Merge/Overlay',
                        'redact'        => 'Redact',
                        'extract'       => 'Extract Pages',
                        'rotate'        => 'Rotate',
                        'flatten'       => 'Flatten',
                        'generate'      => 'Generate',
                    ]),
                TernaryFilter::make('is_active')->label('Active'),
                TernaryFilter::make('auto_apply')->label('Auto-apply'),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('No PDF templates')
            ->emptyStateDescription('Create a template to automatically modify PDFs received via email.');
    }
}
