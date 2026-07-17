<?php

namespace App\Filament\Resources\PendingEmails\Pages;

use App\Enums\PendingEmailStatus;
use App\Filament\Resources\PendingEmails\PendingEmailResource;
use App\Models\PendingEmail;
use App\Services\Imap\EmailApprovalService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPendingEmail extends ViewRecord
{
    protected static string $resource = PendingEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve → Inbox')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (PendingEmail $record) => $record->status === PendingEmailStatus::Pending)
                ->requiresConfirmation()
                ->modalDescription('The email will be moved to Inbox. If you modified attachments, it will be rebuilt.')
                ->action(function (PendingEmail $record) {
                    try {
                        app(EmailApprovalService::class)->approve($record, auth()->user()?->email);
                        Notification::make()->title('Approved and moved to Inbox')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Approval failed')->body($e->getMessage())->danger()->send();
                    }
                    $this->redirect(PendingEmailResource::getUrl('index'));
                }),

            Action::make('editInvoice')
                ->label('Edit invoice data')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->visible(fn (PendingEmail $record) => $record->status === PendingEmailStatus::Pending)
                ->fillForm(fn (PendingEmail $record) => [
                    'invoice_number'  => $record->invoice_number,
                    'invoice_date'    => $record->invoice_date?->format('Y-m-d'),
                    'due_date'        => $record->due_date?->format('Y-m-d'),
                    'invoice_issuer'  => $record->invoice_issuer,
                    'extracted_amount' => $record->extracted_amount,
                    'vat_amount'      => $record->vat_amount,
                    'extracted_currency' => $record->extracted_currency,
                    'extracted_direction' => $record->extracted_direction,
                    'bank_account_id' => $record->bank_account_id,
                    'category'        => $record->category?->value,
                    'tag'             => $record->tag,
                    'notes'           => $record->notes,
                ])
                ->form([
                    Select::make('bank_account_id')
                        ->label('Bank account')
                        ->relationship('bankAccount', 'label')
                        ->searchable()->preload()->placeholder('—'),
                    Select::make('category')
                        ->label('Category')
                        ->options(\App\Enums\EmailCategory::options())
                        ->placeholder('—'),
                    TextInput::make('tag')->label('Tag')->placeholder('e.g. invoice, import'),
                    TextInput::make('invoice_number')->label('Invoice no.')->placeholder('e.g. FAC-2024-001'),
                    DatePicker::make('invoice_date')->label('Invoice date')->displayFormat('d.m.Y'),
                    DatePicker::make('due_date')->label('Due date')->displayFormat('d.m.Y'),
                    TextInput::make('invoice_issuer')->label('Issuer')->placeholder('e.g. Supplier Ltd'),
                    TextInput::make('extracted_amount')->label('Amount')->numeric()->step(0.01),
                    TextInput::make('vat_amount')->label('VAT')->numeric()->step(0.01),
                    Select::make('extracted_currency')
                        ->label('Currency')
                        ->options(['RON' => 'RON', 'EUR' => 'EUR', 'USD' => 'USD', 'GBP' => 'GBP'])
                        ->default('RON'),
                    Select::make('extracted_direction')
                        ->label('Direction')
                        ->options(['debit' => 'Debit (expense)', 'credit' => 'Credit (income)'])
                        ->placeholder('—'),
                    Textarea::make('notes')->label('Notes')->rows(2)->columnSpanFull(),
                ])
                ->action(function (PendingEmail $record, array $data) {
                    $record->update([
                        'bank_account_id'      => $data['bank_account_id'] ?? null,
                        'category'             => $data['category'] ?? null,
                        'tag'                  => $data['tag'] ?? null,
                        'invoice_number'       => $data['invoice_number'] ?? null,
                        'invoice_date'         => $data['invoice_date'] ?? null,
                        'due_date'             => $data['due_date'] ?? null,
                        'invoice_issuer'       => $data['invoice_issuer'] ?? null,
                        'extracted_amount'     => $data['extracted_amount'] ?? null,
                        'vat_amount'           => $data['vat_amount'] ?? null,
                        'extracted_currency'   => $data['extracted_currency'] ?? null,
                        'extracted_direction'  => $data['extracted_direction'] ?? null,
                        'notes'                => $data['notes'] ?? null,
                    ]);
                    Notification::make()->title('Data updated')->success()->send();
                    $this->refreshRecord();
                }),

            Action::make('retryHold')
                ->label('Retry → Hold')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn (PendingEmail $record) => $record->status === PendingEmailStatus::Failed)
                ->requiresConfirmation()
                ->modalHeading('Retry moving to Hold')
                ->modalDescription('The system will search for the email in Inbox and move it to the Hold folder for review. Make sure the email is still in Inbox.')
                ->action(function (PendingEmail $record) {
                    try {
                        app(EmailApprovalService::class)->retryHold($record);
                        Notification::make()->title('Moved to Hold — now pending')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Retry failed')->body($e->getMessage())->danger()->send();
                    }
                    $this->refreshRecord();
                }),

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn (PendingEmail $record) => $record->status === PendingEmailStatus::Pending)
                ->form([
                    Textarea::make('notes')
                        ->label('Rejection reason')
                        ->columnSpanFull(),
                ])
                ->action(function (PendingEmail $record, array $data) {
                    try {
                        app(EmailApprovalService::class)->reject($record, auth()->user()?->email);
                        if (filled($data['notes'])) {
                            $record->update(['notes' => $data['notes']]);
                        }
                        Notification::make()->title('Rejected')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Rejection failed')->body($e->getMessage())->danger()->send();
                    }
                    $this->redirect(PendingEmailResource::getUrl('index'));
                }),

            // ── PDF Modification Actions ────────────────────────────────────

            Action::make('pdfWatermark')
                ->label('Add PDF Watermark')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->visible(fn (PendingEmail $record) =>
                    $record->status === PendingEmailStatus::Pending
                    && $record->pdfAttachments()->exists()
                )
                ->form([
                    TextInput::make('text')
                        ->label('Watermark text')
                        ->default(fn () => \App\Models\AppSetting::current()->pdf_watermark_text ?? 'CONFIDENTIAL')
                        ->required(),
                    TextInput::make('font_size')
                        ->label('Font size')
                        ->numeric()
                        ->default(60),
                    TextInput::make('color')
                        ->label('Color (hex)')
                        ->default('#CCCCCC'),
                    TextInput::make('opacity')
                        ->label('Opacity (0-1)')
                        ->numeric()
                        ->default(0.3)
                        ->step(0.1),
                    TextInput::make('angle')
                        ->label('Angle (degrees)')
                        ->numeric()
                        ->default(-45),
                    Select::make('attachment_id')
                        ->label('PDF attachment')
                        ->options(fn (PendingEmail $record) =>
                            $record->pdfAttachments()->pluck('filename', 'id')->all()
                        )
                        ->required(),
                ])
                ->action(function (PendingEmail $record, array $data) {
                    try {
                        $attachment = $record->pdfAttachments()->findOrFail($data['attachment_id']);
                        $pdfService = app(\App\Services\PdfModifierService::class);

                        $result = $pdfService->apply(
                            $attachment->path,
                            $attachment->disk,
                            [['type' => 'watermark', 'params' => [
                                'text'      => $data['text'],
                                'font_size' => (int) $data['font_size'],
                                'color'     => $data['color'],
                                'opacity'   => (float) $data['opacity'],
                                'angle'     => (int) $data['angle'],
                            ]]],
                            'watermarked-' . $attachment->filename
                        );

                        // Create new attachment with modified PDF
                        $record->attachments()->create([
                            'filename'   => 'watermarked-' . $attachment->filename,
                            'mime_type'  => 'application/pdf',
                            'size'       => $result['size'],
                            'disk'       => $result['disk'],
                            'path'       => $result['path'],
                            'is_inline'  => false,
                            'is_replaced' => false,
                        ]);

                        $record->update([
                            'modified'                => true,
                            'pdf_modified'            => true,
                            'pdf_pages_modified'      => $result['pages'],
                            'pdf_modification_summary' => trim(($record->pdf_modification_summary ?? '') . "\nWatermark: " . $data['text']),
                        ]);

                        \App\Models\PdfModificationLog::create([
                            'pending_email_id'   => $record->id,
                            'pending_email_attachment_id' => $attachment->id,
                            'operation'          => 'watermark',
                            'params'             => $data,
                            'result'             => $result,
                            'status'             => 'completed',
                            'performed_by'       => auth()->user()?->email,
                        ]);

                        Notification::make()->title('Watermark applied to PDF')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Watermark error')->body($e->getMessage())->danger()->send();
                    }
                    $this->refreshRecord();
                }),

            Action::make('pdfStamp')
                ->label('Stamp PDF')
                ->icon('heroicon-o-stamp')
                ->color('success')
                ->visible(fn (PendingEmail $record) =>
                    $record->status === PendingEmailStatus::Pending
                    && $record->pdfAttachments()->exists()
                )
                ->form([
                    TextInput::make('text')
                        ->label('Stamp text')
                        ->default(fn () => \App\Models\AppSetting::current()->pdf_stamp_text ?? 'PROCESSED')
                        ->required(),
                    TextInput::make('operator')
                        ->label('Operator')
                        ->default(fn () => \App\Models\AppSetting::current()->pdf_stamp_operator ?? auth()->user()?->name),
                    Select::make('position')
                        ->label('Position')
                        ->options([
                            'top-left'     => 'Top left',
                            'top-right'    => 'Top right',
                            'bottom-left'  => 'Bottom left',
                            'bottom-right' => 'Bottom right',
                            'center'       => 'Center',
                        ])
                        ->default('bottom-right'),
                    Select::make('attachment_id')
                        ->label('PDF attachment')
                        ->options(fn (PendingEmail $record) =>
                            $record->pdfAttachments()->pluck('filename', 'id')->all()
                        )
                        ->required(),
                ])
                ->action(function (PendingEmail $record, array $data) {
                    try {
                        $attachment = $record->pdfAttachments()->findOrFail($data['attachment_id']);
                        $pdfService = app(\App\Services\PdfModifierService::class);

                        $result = $pdfService->apply(
                            $attachment->path,
                            $attachment->disk,
                            [['type' => 'stamp', 'params' => [
                                'text'     => $data['text'],
                                'operator' => $data['operator'],
                                'position' => $data['position'],
                            ]]],
                            'stamped-' . $attachment->filename
                        );

                        $record->attachments()->create([
                            'filename'   => 'stamped-' . $attachment->filename,
                            'mime_type'  => 'application/pdf',
                            'size'       => $result['size'],
                            'disk'       => $result['disk'],
                            'path'       => $result['path'],
                            'is_inline'  => false,
                        ]);

                        $record->update([
                            'modified'                => true,
                            'pdf_modified'            => true,
                            'pdf_pages_modified'      => $result['pages'],
                            'pdf_modification_summary' => trim(($record->pdf_modification_summary ?? '') . "\nStamp: " . $data['text']),
                        ]);

                        \App\Models\PdfModificationLog::create([
                            'pending_email_id'   => $record->id,
                            'pending_email_attachment_id' => $attachment->id,
                            'operation'          => 'stamp',
                            'params'             => $data,
                            'result'             => $result,
                            'status'             => 'completed',
                            'performed_by'       => auth()->user()?->email,
                        ]);

                        Notification::make()->title('Stamp applied to PDF')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Stamp error')->body($e->getMessage())->danger()->send();
                    }
                    $this->refreshRecord();
                }),

            Action::make('pdfExtractPages')
                ->label('Extract PDF Pages')
                ->icon('heroicon-o-scissors')
                ->color('warning')
                ->visible(fn (PendingEmail $record) =>
                    $record->status === PendingEmailStatus::Pending
                    && $record->pdfAttachments()->exists()
                )
                ->form([
                    TextInput::make('pages')
                        ->label('Pages to extract')
                        ->placeholder('1-3,5,7-9')
                        ->helperText('Format: 1,3,5 or 1-3,5,7-9')
                        ->required(),
                    Select::make('attachment_id')
                        ->label('PDF attachment')
                        ->options(fn (PendingEmail $record) =>
                            $record->pdfAttachments()->pluck('filename', 'id')->all()
                        )
                        ->required(),
                ])
                ->action(function (PendingEmail $record, array $data) {
                    try {
                        $attachment = $record->pdfAttachments()->findOrFail($data['attachment_id']);
                        $pdfService = app(\App\Services\PdfModifierService::class);

                        $result = $pdfService->apply(
                            $attachment->path,
                            $attachment->disk,
                            [['type' => 'extract', 'params' => ['pages' => $data['pages']]]],
                            'extracted-' . $attachment->filename
                        );

                        $record->attachments()->create([
                            'filename'   => 'extracted-' . $attachment->filename,
                            'mime_type'  => 'application/pdf',
                            'size'       => $result['size'],
                            'disk'       => $result['disk'],
                            'path'       => $result['path'],
                            'is_inline'  => false,
                        ]);

                        $record->update([
                            'modified'                => true,
                            'pdf_modified'            => true,
                            'pdf_pages_modified'      => $result['pages'],
                            'pdf_modification_summary' => trim(($record->pdf_modification_summary ?? '') . "\nExtract pages: " . $data['pages']),
                        ]);

                        \App\Models\PdfModificationLog::create([
                            'pending_email_id'   => $record->id,
                            'pending_email_attachment_id' => $attachment->id,
                            'operation'          => 'extract',
                            'params'             => $data,
                            'result'             => $result,
                            'status'             => 'completed',
                            'performed_by'       => auth()->user()?->email,
                        ]);

                        Notification::make()->title("Pages extracted: {$result['pages']} pages")->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Extraction error')->body($e->getMessage())->danger()->send();
                    }
                    $this->refreshRecord();
                }),

            Action::make('pdfRedact')
                ->label('Redact PDF')
                ->icon('heroicon-o-eye-slash')
                ->color('danger')
                ->visible(fn (PendingEmail $record) =>
                    $record->status === PendingEmailStatus::Pending
                    && $record->pdfAttachments()->exists()
                )
                ->form([
                    \Filament\Forms\Components\Repeater::make('zones')
                        ->label('Zones to redact')
                        ->schema([
                            TextInput::make('x')->label('X')->numeric()->default(0),
                            TextInput::make('y')->label('Y')->numeric()->default(0),
                            TextInput::make('width')->label('Width')->numeric()->default(100),
                            TextInput::make('height')->label('Height')->numeric()->default(20),
                            TextInput::make('page')->label('Page')->default('all'),
                        ])
                        ->columns(5)
                        ->defaultItems(1),
                    Select::make('attachment_id')
                        ->label('PDF attachment')
                        ->options(fn (PendingEmail $record) =>
                            $record->pdfAttachments()->pluck('filename', 'id')->all()
                        )
                        ->required(),
                ])
                ->action(function (PendingEmail $record, array $data) {
                    try {
                        $attachment = $record->pdfAttachments()->findOrFail($data['attachment_id']);
                        $pdfService = app(\App\Services\PdfModifierService::class);

                        $result = $pdfService->apply(
                            $attachment->path,
                            $attachment->disk,
                            [['type' => 'redact', 'params' => ['zones' => $data['zones']]]],
                            'redacted-' . $attachment->filename
                        );

                        $record->attachments()->create([
                            'filename'   => 'redacted-' . $attachment->filename,
                            'mime_type'  => 'application/pdf',
                            'size'       => $result['size'],
                            'disk'       => $result['disk'],
                            'path'       => $result['path'],
                            'is_inline'  => false,
                        ]);

                        $record->update([
                            'modified'                => true,
                            'pdf_modified'            => true,
                            'pdf_modification_summary' => trim(($record->pdf_modification_summary ?? '') . "\nRedact: " . count($data['zones']) . ' zone'),
                        ]);

                        \App\Models\PdfModificationLog::create([
                            'pending_email_id'   => $record->id,
                            'pending_email_attachment_id' => $attachment->id,
                            'operation'          => 'redact',
                            'params'             => $data,
                            'result'             => $result,
                            'status'             => 'completed',
                            'performed_by'       => auth()->user()?->email,
                        ]);

                        Notification::make()->title('Zones redacted from PDF')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Redaction error')->body($e->getMessage())->danger()->send();
                    }
                    $this->refreshRecord();
                }),

            Action::make('pdfApplyTemplate')
                ->label('Apply PDF Template')
                ->icon('heroicon-o-squares-2x2')
                ->color('primary')
                ->visible(fn (PendingEmail $record) =>
                    $record->status === PendingEmailStatus::Pending
                    && $record->pdfAttachments()->exists()
                    && \App\Models\PdfTemplate::where('is_active', true)->exists()
                )
                ->form([
                    Select::make('template_id')
                        ->label('Template')
                        ->options(fn () => \App\Models\PdfTemplate::where('is_active', true)
                            ->pluck('name', 'id')
                            ->all()
                        )
                        ->required(),
                    Select::make('attachment_id')
                        ->label('PDF attachment')
                        ->options(fn (PendingEmail $record) =>
                            $record->pdfAttachments()->pluck('filename', 'id')->all()
                        )
                        ->required(),
                ])
                ->action(function (PendingEmail $record, array $data) {
                    try {
                        $template = \App\Models\PdfTemplate::findOrFail($data['template_id']);
                        $attachment = $record->pdfAttachments()->findOrFail($data['attachment_id']);
                        $pdfService = app(\App\Services\PdfModifierService::class);

                        $result = $pdfService->apply(
                            $attachment->path,
                            $attachment->disk,
                            [$template->toOperationParams()],
                            'tmpl-' . $attachment->filename
                        );

                        $record->attachments()->create([
                            'filename'   => 'tmpl-' . $attachment->filename,
                            'mime_type'  => 'application/pdf',
                            'size'       => $result['size'],
                            'disk'       => $result['disk'],
                            'path'       => $result['path'],
                            'is_inline'  => false,
                        ]);

                        $record->update([
                            'modified'                => true,
                            'pdf_modified'            => true,
                            'pdf_pages_modified'      => $result['pages'],
                            'pdf_modification_summary' => trim(($record->pdf_modification_summary ?? '') . "\nTemplate: " . $template->name),
                        ]);

                        \App\Models\PdfModificationLog::create([
                            'pending_email_id'   => $record->id,
                            'pending_email_attachment_id' => $attachment->id,
                            'pdf_template_id'    => $template->id,
                            'operation'          => $template->type,
                            'params'             => $template->config,
                            'result'             => $result,
                            'status'             => 'completed',
                            'performed_by'       => auth()->user()?->email,
                        ]);

                        Notification::make()->title("Template \"{$template->name}\" applied")->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Template error')->body($e->getMessage())->danger()->send();
                    }
                    $this->refreshRecord();
                }),
        ];
    }
}
