<?php

namespace App\Filament\Resources\EmailFilterRules\Tables;

use App\Enums\EmailCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmailFilterRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('priority')
            ->defaultSort('priority')
            ->columns([
                TextColumn::make('priority')->label('#')->sortable()->badge()->color('gray'),
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('match_type')->label('Match')
                    ->formatStateUsing(fn ($state) => $state === 'any' ? 'ANY' : 'ALL')->badge()
                    ->color(fn ($state) => $state === 'any' ? 'warning' : 'info'),
                TextColumn::make('bankAccount.label')->label('Bank Account')->placeholder('—')->toggleable(),
                TextColumn::make('then_category')->label('Category')->badge()->placeholder('—'),
                TextColumn::make('emailAccount.name')
                    ->label('Email Account')
                    ->placeholder('All')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('matched_emails_count')
                    ->counts('matchedEmails')
                    ->label('Matches')
                    ->badge()
                    ->color('info'),
                IconColumn::make('then_reject')->label('Auto-reject')->boolean()
                    ->trueIcon('heroicon-o-x-circle')->trueColor('danger')
                    ->falseIcon('heroicon-o-minus')->falseColor('gray'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->striped()
            ->emptyStateIcon('heroicon-o-funnel')
            ->emptyStateHeading('No rules')
            ->emptyStateDescription('Create your first filter rule.')
            ->filters([
                TernaryFilter::make('is_active')->label('Status'),
                SelectFilter::make('match_type')->options(['all'=>'ALL','any'=>'ANY']),
                SelectFilter::make('then_category')->options(EmailCategory::options())->label('Action category'),
                SelectFilter::make('email_account_id')->relationship('emailAccount','name')->label('Email Account'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
