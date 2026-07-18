<?php

namespace App\Filament\Resources\PdfModificationLogs;

use App\Filament\Resources\PdfModificationLogs\Pages\ListPdfModificationLogs;
use App\Filament\Resources\PdfModificationLogs\Pages\ViewPdfModificationLog;
use App\Models\PdfModificationLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PdfModificationLogResource extends Resource
{
    protected static ?string $model = PdfModificationLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|UnitEnum|null $navigationGroup = 'Email Customs';
    protected static ?string $navigationLabel = 'PDF Logs';
    protected static ?string $modelLabel = 'PDF Modification Log';
    protected static ?string $pluralModelLabel = 'PDF Modification Logs';
    protected static ?int $navigationSort = 7;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                TextColumn::make('operation')
                    ->label('Operation')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'watermark'       => 'info',
                        'stamp'           => 'success',
                        'merge'           => 'primary',
                        'redact'          => 'danger',
                        'extract'         => 'warning',
                        'rotate'          => 'gray',
                        'flatten'         => 'gray',
                        'add_header_footer' => 'info',
                        'replace_text'    => 'warning',
                        'generate'        => 'success',
                        default           => 'gray',
                    }),

                TextColumn::make('pendingEmail.email')
                    ->label('Email')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('attachment.filename')
                    ->label('Attachment')
                    ->limit(25)
                    ->toggleable(),

                TextColumn::make('template.name')
                    ->label('Template')
                    ->limit(20)
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed'  => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('performed_by')
                    ->label('Operator')
                    ->limit(20)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPdfModificationLogs::route('/'),
            'view'   => ViewPdfModificationLog::route('/{record}'),
        ];
    }
}
