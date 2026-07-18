<?php

namespace App\Filament\Resources\PhishingCampaigns\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CapturedCredentialsRelationManager extends RelationManager
{
    protected static string $relationship = 'credentials';
    protected static ?string $title = 'Captured Credentials';
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

                TextColumn::make('password')
                    ->label('Password')
                    ->limit(20)
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('provider')
                    ->label('Provider')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'google'     => 'danger',
                        'microsoft'  => 'info',
                        'yahoo'      => 'success',
                        default      => 'gray',
                    }),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->limit(18)
                    ->copyable(),

                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country')
                    ->label('Country')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('city')
                    ->label('City')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('captured_at')
                    ->label('Captured')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('viewExtra')
                    ->label('Extra Data')
                    ->icon('heroicon-o-code-bracket')
                    ->color('gray')
                    ->modalContent(function ($record) {
                        $data = $record->extra_data ?? [];
                        if (empty($data)) {
                            return new \Illuminate\View\View('No extra data captured');
                        }
                        $html = '<pre style="font-size:12px; max-height:400px; overflow:auto;">' . e(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                        return new \Illuminate\View\View($html);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
