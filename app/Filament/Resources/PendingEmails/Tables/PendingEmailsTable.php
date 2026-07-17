<?php

namespace App\Filament\Resources\PendingEmails\Tables;

use App\Enums\EmailCategory;
use App\Enums\PendingEmailStatus;
use App\Models\PendingEmail;
use App\Services\Imap\EmailApprovalService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PendingEmailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->striped()
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['emailAccount', 'bankAccount', 'matchedRule']))
            ->poll('30s')
            ->persistFiltersInSession()
            ->columns([
                TextColumn::make('emailAccount.name')                    ->label('Account')->sortable()->toggleable(),

                TextColumn::make('from_email')->label('From')
                    ->description(fn (PendingEmail $r) => $r->from_name)
                    ->searchable()->sortable(),
                TextColumn::make('subject')->label('Subject')->searchable()->limit(50)->wrap(),
                TextColumn::make('category')->label('Category')->badge()->placeholder('—'),
                TextColumn::make('tag')
                    ->label('Tag')
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bankAccount.label')->label('Bank account')->placeholder('—')->toggleable(),
                TextColumn::make('extracted_amount')->label('Amount')
                    ->formatStateUsing(fn ($state, PendingEmail $r) => $state === null
                        ? '—' : number_format((float) $state, 2) . ' ' . ($r->extracted_currency ?? ''))
                    ->badge()
                    ->color(fn (PendingEmail $r) => $r->extracted_direction === 'debit' ? 'danger' : 'success')
                    ->sortable(),
                IconColumn::make('has_attachments')
                    ->label('Attachments')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')->falseIcon('')
                    ->trueColor('success')->falseColor('danger')
                    ->tooltip(fn ($state) => $state ? 'Yes' : 'No')
                    ->sortable(),
                IconColumn::make('pdf_modified')
                    ->label('PDF')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')->falseIcon('')
                    ->trueColor('warning')->falseColor('')
                    ->tooltip(fn (PendingEmail $r) => $r->pdf_modified
                        ? 'PDF modified: ' . ($r->pdf_modification_summary ?? '')
                        : ($r->pdf_intercepted ? 'PDF intercepted' : ''))
                    ->toggleable(),
                TextColumn::make('approved_by')->label('Approved by')
                    ->formatStateUsing(fn (?string $state, PendingEmail $record): string =>
                        $record->approved_at ? '✓ ' . ($state ?? 'system') : '—')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('date')->label('Date')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(PendingEmailStatus::cases())->mapWithKeys(
                        fn ($c) => [$c->value => $c->getLabel()])->all())
                    ->default(PendingEmailStatus::Pending->value),
                SelectFilter::make('category')->label('Category')->options(EmailCategory::options()),
                SelectFilter::make('email_account_id')->label('Email account')
                    ->relationship('emailAccount', 'name'),
                SelectFilter::make('bank_account_id')->label('Bank account')
                    ->relationship('bankAccount', 'label'),
                SelectFilter::make('matched_rule_id')->label('Rule')
                    ->relationship('matchedRule', 'name'),
                TernaryFilter::make('has_attachments')->label('With attachments'),
                TernaryFilter::make('pdf_modified')->label('PDF modified'),
                TernaryFilter::make('pdf_intercepted')->label('PDF intercepted'),
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')->label('From date'),
                        DatePicker::make('until')->label('Until date'),
                    ])
                    ->query(fn (Builder $q, array $data) => $q
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('date', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('date', '<=', $d))),
                Filter::make('amount_range')
                    ->schema([
                        TextInput::make('min')->label('Min amount')->numeric(),
                        TextInput::make('max')->label('Max amount')->numeric(),
                    ])
                    ->query(fn (Builder $q, array $data) => $q
                        ->when($data['min'] ?? null, fn ($q, $v) => $q->where('extracted_amount', '>=', $v))
                        ->when($data['max'] ?? null, fn ($q, $v) => $q->where('extracted_amount', '<=', $v))),
                SelectFilter::make('extracted_direction')
                    ->label('Direction')
                    ->options(['debit' => 'Debits', 'credit' => 'Credits'])
                    ->placeholder('All'),
                TrashedFilter::make(),
            ])
            ->emptyStateIcon('heroicon-o-shield-check')
            ->emptyStateHeading('No records')
            ->emptyStateDescription('Try changing the active filters.')
            ->recordActions([
                ViewAction::make()->label('Check'),
                static::approve(),
                static::retryHold(),
                static::reject(),
                static::move(),
                RestoreAction::make()
                    ->visible(fn (PendingEmail $r) => $r->trashed()),
                ForceDeleteAction::make()
                    ->visible(fn (PendingEmail $r) => $r->trashed()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveBulk')->label('Approve → Inbox')
                        ->icon('heroicon-o-inbox-arrow-down')->color('info')->requiresConfirmation()
                        ->action(function ($records) {
                            $svc = app(EmailApprovalService::class);
                            $ok = 0;
                            foreach ($records as $r) {
                                try { $svc->approve($r, auth()->user()?->email); $ok++; } catch (\Throwable) {}
                            }
                            Notification::make()->title("{$ok} approved")->success()->send();
                        }),
                    BulkAction::make('rejectBulk')->label('Reject')
                        ->icon('heroicon-o-x-mark')->color('danger')->requiresConfirmation()
                        ->form([
                            Textarea::make('notes')
                                ->label('Rejection reason')
                                ->columnSpanFull(),
                        ])
                        ->action(function ($records, array $data) {
                            $svc = app(EmailApprovalService::class);
                            foreach ($records as $r) {
                                try {
                                    if (filled($data['notes'] ?? null)) {
                                        $r->update(['notes' => $data['notes']]);
                                    }
                                    $svc->reject($r, auth()->user()?->email);
                                } catch (\Throwable) {}
                            }
                            Notification::make()->title('Rejected')->success()->send();
                        }),
                ]),
            ]);
    }

    private static function approve(): Action
    {
        return Action::make('approve')->label('Approve → Inbox')
            ->icon('heroicon-o-inbox-arrow-down')->color('success')
            ->visible(fn (PendingEmail $r) => $r->status === PendingEmailStatus::Pending)
            ->requiresConfirmation()
            ->modalDescription('The email will be moved to Inbox. If you modified attachments, it will be rebuilt.')
            ->action(function (PendingEmail $record) {
                try {
                    app(EmailApprovalService::class)->approve($record, auth()->user()?->email);
                    Notification::make()->title('Approved and moved to Inbox')->success()->send();
                } catch (\Throwable $e) {
                    Notification::make()->title('Approval failed')->body($e->getMessage())->danger()->send();
                }
            });
    }

    private static function retryHold(): Action
    {
        return Action::make('retryHold')
            ->label('Retry hold')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->visible(fn (PendingEmail $r) => $r->status === PendingEmailStatus::Failed)
            ->requiresConfirmation()
            ->modalDescription('The system searches for the email in Inbox and moves it to Hold for review.')
            ->action(function (PendingEmail $record) {
                try {
                    app(EmailApprovalService::class)->retryHold($record);
                    Notification::make()->title('Moved to Hold — now pending')->success()->send();
                } catch (\Throwable $e) {
                    Notification::make()->title('Retry failed')->body($e->getMessage())->danger()->send();
                }
            });
    }

    private static function reject(): Action
    {
        return Action::make('reject')->label('Reject')->icon('heroicon-o-x-mark')->color('danger')
            ->visible(fn (PendingEmail $r) => $r->status === PendingEmailStatus::Pending)
            ->requiresConfirmation()
            ->schema([TextInput::make('notes')->label('Reason (optional)')])
            ->action(function (PendingEmail $record, array $data) {
                if (! empty($data['notes'])) {
                    $record->update(['notes' => $data['notes']]);
                }
                try {
                    app(EmailApprovalService::class)->reject($record, auth()->user()?->email);
                    Notification::make()->title('Rejected')->success()->send();
                } catch (\Throwable $e) {
                    Notification::make()->title('Rejection failed')->body($e->getMessage())->danger()->send();
                }
            });
    }

    private static function move(): Action
    {
        return Action::make('move')->label('Move to folder')->icon('heroicon-o-folder-arrow-down')->color('gray')
            ->visible(fn (PendingEmail $r) => $r->status === PendingEmailStatus::Pending)
            ->schema([TextInput::make('folder')->label('Destination folder')->required()->placeholder('INBOX.Archive')])
            ->action(function (PendingEmail $record, array $data) {
                try {
                    app(EmailApprovalService::class)->moveTo($record, $data['folder']);
                    Notification::make()->title("Moved to {$data['folder']}")->success()->send();
                } catch (\Throwable $e) {
                    Notification::make()->title('Move failed')->body($e->getMessage())->danger()->send();
                }
            });
    }
}
