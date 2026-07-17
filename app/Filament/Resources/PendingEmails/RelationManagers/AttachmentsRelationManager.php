<?php

namespace App\Filament\Resources\PendingEmails\RelationManagers;

use App\Models\PendingEmailAttachment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Managing attachments for a quarantined email: download, delete, replace (e.g. alternate
 * PDF) and add. Any modification marks the email as `modified` → rebuild MIME on approval.
 */
class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';
    protected static ?string $title = 'Attachments';

    public function table(Table $table): Table
    {
        $disk = fn () => config('email-customs.attachments_disk', 'local');

        return $table
            ->recordTitleAttribute('filename')
            ->columns([
                IconColumn::make('mime_type')
                    ->label('')
                    ->icon(fn (PendingEmailAttachment $r) => $r->isPdf()
                        ? 'heroicon-o-document-text' : 'heroicon-o-paper-clip'),
                TextColumn::make('filename')->label('File')->searchable()->wrap(),
                TextColumn::make('mime_type')->label('Type')->toggleable(),
                TextColumn::make('size')->label('Size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 1) . ' KB'),
                IconColumn::make('is_inline')->label('Inline')->boolean(),
                IconColumn::make('is_removed')->label('Removed')->boolean()->trueColor('danger'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add file')
                    ->schema([
                        TextInput::make('filename')->label('File name')->required(),
                        FileUpload::make('upload')->label('File')
                            ->disk($disk())
                            ->directory('email-customs/' . $this->getOwnerRecord()->id)
                            ->required(),
                    ])
                    ->using(function (array $data) use ($disk) {
                        $d = $disk();
                        $record = $this->getOwnerRecord()->attachments()->create([
                            'filename'  => $data['filename'],
                            'mime_type' => Storage::disk($d)->mimeType($data['upload']) ?: 'application/octet-stream',
                            'size'      => Storage::disk($d)->size($data['upload']),
                            'disk'      => $d,
                            'path'      => $data['upload'],
                            'is_inline' => false,
                        ]);
                        $this->getOwnerRecord()->update(['modified' => true, 'has_attachments' => true]);

                        return $record;
                    }),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')->icon('heroicon-o-arrow-down-tray')->color('gray')
                    ->action(fn (PendingEmailAttachment $record): StreamedResponse =>
                        Storage::disk($record->disk)->download($record->path, $record->filename)),

                Action::make('replace')
                    ->label('Replace')->icon('heroicon-o-arrow-path')->color('warning')
                    ->schema([
                        FileUpload::make('upload')->label('New file')
                            ->disk($disk())
                            ->directory('email-customs/' . $this->getOwnerRecord()->id)
                            ->required(),
                    ])
                    ->action(function (PendingEmailAttachment $record, array $data) {
                        $d = $record->disk;
                        if (Storage::disk($d)->exists($record->path)) {
                            Storage::disk($d)->delete($record->path);
                        }
                        $record->update([
                            'path'        => $data['upload'],
                            'mime_type'   => Storage::disk($d)->mimeType($data['upload']) ?: $record->mime_type,
                            'size'        => Storage::disk($d)->size($data['upload']),
                            'is_replaced' => true,
                            'is_removed'  => false,
                        ]);
                        $this->getOwnerRecord()->update(['modified' => true]);
                        Notification::make()->title('Attachment replaced')->success()->send();
                    }),

                Action::make('toggleRemoved')
                    ->label(fn (PendingEmailAttachment $r) => $r->is_removed ? 'Reactivate' : 'Remove')
                    ->icon(fn (PendingEmailAttachment $r) => $r->is_removed
                        ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-trash')
                    ->color(fn (PendingEmailAttachment $r) => $r->is_removed ? 'gray' : 'danger')
                    ->action(function (PendingEmailAttachment $record) {
                        $record->update(['is_removed' => ! $record->is_removed]);
                        $this->getOwnerRecord()->update(['modified' => true]);
                    }),

                Action::make('pdfInfo')
                    ->label('Info PDF')
                    ->icon('heroicon-o-information-circle')
                    ->color('info')
                    ->visible(fn (PendingEmailAttachment $r) => $r->isPdf())
                    ->action(function (PendingEmailAttachment $record) {
                        $pdfService = app(\App\Services\PdfModifierService::class);
                        $info = $pdfService->getInfo($record->contents());

                        $modalText = "**Pages:** {$info['pages']}\n";
                        $modalText .= "**Size:** " . number_format($record->size / 1024, 1) . " KB\n\n";
                        if (! empty($info['text'])) {
                            $textPreview = substr($info['text'], 0, 500);
                            $modalText .= "**Extracted text (preview):**\n{$textPreview}";
                        }

                        Notification::make()
                            ->title('PDF details')
                            ->body($modalText)
                            ->success()
                            ->send();
                    }),

                Action::make('pdfStampQuick')
                    ->label('Quick stamp')
                    ->icon('heroicon-o-stamp')
                    ->color('success')
                    ->visible(fn (PendingEmailAttachment $r) => $r->isPdf())
                    ->form([
                        \Filament\Forms\Components\TextInput::make('text')
                            ->label('Text')
                            ->default(fn () => \App\Models\AppSetting::current()->pdf_stamp_text ?? 'PROCESAT'),
                        \Filament\Forms\Components\TextInput::make('operator')
                            ->label('Operator')
                            ->default(fn () => auth()->user()?->email),
                    ])
                    ->action(function (PendingEmailAttachment $record, array $data) {
                        try {
                            $pdfService = app(\App\Services\PdfModifierService::class);

                            $result = $pdfService->apply(
                                $record->path,
                                $record->disk,
                                [['type' => 'stamp', 'params' => [
                                    'text'     => $data['text'],
                                    'operator' => $data['operator'],
                                    'position' => 'bottom-right',
                                ]]],
                                'stamped-' . $record->filename
                            );

                            $pending = $this->getOwnerRecord();
                            $pending->attachments()->create([
                                'filename'   => 'stamped-' . $record->filename,
                                'mime_type'  => 'application/pdf',
                                'size'       => $result['size'],
                                'disk'       => $result['disk'],
                                'path'       => $result['path'],
                                'is_inline'  => false,
                            ]);

                            $pending->update([
                                'modified'     => true,
                                'pdf_modified' => true,
                            ]);

                            Notification::make()->title('Stamp applied')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
                        }
                    }),

                Action::make('pdfWatermarkQuick')
                    ->label('Quick watermark')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->visible(fn (PendingEmailAttachment $r) => $r->isPdf())
                    ->form([
                        \Filament\Forms\Components\TextInput::make('text')
                            ->label('Text')
                            ->default(fn () => \App\Models\AppSetting::current()->pdf_watermark_text ?? 'CONFIDENTIAL'),
                    ])
                    ->action(function (PendingEmailAttachment $record, array $data) {
                        try {
                            $pdfService = app(\App\Services\PdfModifierService::class);

                            $result = $pdfService->apply(
                                $record->path,
                                $record->disk,
                                [['type' => 'watermark', 'params' => [
                                    'text'      => $data['text'],
                                    'font_size' => 60,
                                    'color'     => '#CCCCCC',
                                    'opacity'   => 0.3,
                                    'angle'     => -45,
                                ]]],
                                'watermarked-' . $record->filename
                            );

                            $pending = $this->getOwnerRecord();
                            $pending->attachments()->create([
                                'filename'   => 'watermarked-' . $record->filename,
                                'mime_type'  => 'application/pdf',
                                'size'       => $result['size'],
                                'disk'       => $result['disk'],
                                'path'       => $result['path'],
                                'is_inline'  => false,
                            ]);

                            $pending->update([
                                'modified'     => true,
                                'pdf_modified' => true,
                            ]);

                            Notification::make()->title('Watermark applied')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
