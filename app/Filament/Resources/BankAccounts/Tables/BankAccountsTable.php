<?php

namespace App\Filament\Resources\BankAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('Label')->searchable()->sortable(),
                TextColumn::make('bank_name')->label('Bank')->searchable()->toggleable(),
                TextColumn::make('iban')->label('IBAN')->searchable()->copyable()->toggleable(),
                TextColumn::make('account_holder')
                    ->label('Account Holder')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency')->label('Currency')->badge(),
                TextColumn::make('pending_emails_count')
                    ->counts('pendingEmails')->label('Emails')->badge()->color('info'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->defaultSort('label')
            ->striped()
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateHeading('No bank accounts')
            ->emptyStateDescription('Add a bank account.')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->label('Delete')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation(),
            ])
            ->recordClasses(fn ($r) => $r?->is_active ? '' : 'opacity-50')
            ->filters([
                TernaryFilter::make('is_active')->label('Status'),
                SelectFilter::make('currency')->options(['RON'=>'RON','EUR'=>'EUR','USD'=>'USD','GBP'=>'GBP']),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
