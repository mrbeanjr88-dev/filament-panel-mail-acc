<?php

namespace App\Filament\Widgets;

use App\Enums\PendingEmailStatus;
use App\Models\PendingEmail;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentEmailsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recently Processed Emails')
            ->description('The 10 most recent approved or processed emails')
            ->query(
                PendingEmail::query()
                    ->whereIn('status', [PendingEmailStatus::Processed->value, PendingEmailStatus::Approved->value])
                    ->with(['bankAccount', 'emailAccount'])
                    ->latest('approved_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('emailAccount.name')
                    ->label('Account')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('from_email')
                    ->label('Sender')
                    ->description(fn (PendingEmail $r) => $r->from_name)
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(40)
                    ->tooltip(fn (PendingEmail $r) => $r->subject),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('bankAccount.label')
                    ->label('Bank account')
                    ->placeholder('—'),
                TextColumn::make('extracted_amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state, PendingEmail $r) => $state === null
                        ? '—' : number_format((float) $state, 2) . ' ' . ($r->extracted_currency ?? ''))
                    ->badge()
                    ->color(fn (PendingEmail $r) => $r->extracted_direction === 'debit' ? 'danger' : 'success'),
                TextColumn::make('approved_by')
                    ->label('Operator')
                    ->formatStateUsing(fn ($state) => $state ? $state : 'system')
                    ->placeholder('—'),
                TextColumn::make('approved_at')
                    ->label('Approved')
                    ->since()
                    ->tooltip(fn (PendingEmail $r) => $r->approved_at?->format('d.m.Y H:i'))
                    ->sortable(),
            ])
            ->emptyStateIcon('heroicon-o-envelope')
            ->emptyStateHeading('No processed emails')
            ->emptyStateDescription('Processed emails will appear here.')
            ->poll('30s')
            ->paginated(false);
    }
}
