<?php

namespace App\Filament\Resources\PhishingCampaigns\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TargetsRelationManager extends RelationManager
{
    protected static string $relationship = 'targets';
    protected static ?string $title = 'Targets';
    protected static ?string $recordTitleAttribute = 'email';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('user_display_name')
                    ->label('Name')
                    ->limit(20)
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'gray',
                        'sent'      => 'info',
                        'opened'    => 'warning',
                        'clicked'   => 'primary',
                        'captured'  => 'success',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),

                IconColumn::make('sent_at')
                    ->label('Sent')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => filled($record->sent_at)),

                IconColumn::make('opened_at')
                    ->label('Opened')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => filled($record->opened_at)),

                IconColumn::make('clicked_at')
                    ->label('Clicked')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => filled($record->clicked_at)),

                IconColumn::make('captured_at')
                    ->label('Captured')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => filled($record->captured_at)),

                TextColumn::make('tracking_token')
                    ->label('Token')
                    ->limit(12)
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('viewDeepLink')
                    ->label('Deep Link')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->url(fn ($record) => route('phish.deep-inject', [
                        'provider' => $record->campaign->provider,
                        'token'    => $record->tracking_token,
                    ]))
                    ->openUrlInNewTab(),

                Action::make('trackPixel')
                    ->label('Track')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn ($record) => route('phish.track', [
                        'token' => $record->tracking_token,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
