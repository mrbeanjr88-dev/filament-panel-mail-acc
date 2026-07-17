<?php

namespace App\Filament\Resources\EmailVerifications\Tables;

use App\Models\EmailVerification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmailVerificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(40)
                    ->copyable(),

                TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->limit(25),

                BadgeColumn::make('provider')
                    ->label('Provider')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'google'           => 'success',
                        'microsoft'        => 'info',
                        'yahoo'            => 'warning',
                        'zoho'             => 'primary',
                        'protonmail'       => 'danger',
                        'yandex'           => 'gray',
                        'icloud'           => 'gray',
                        'qq'               => 'warning',
                        'naver'            => 'info',
                        'gmx'              => 'success',
                        'web_de'           => 'success',
                        'ionos'            => 'info',
                        'telekom'          => 'info',
                        'freenet'          => 'info',
                        'a1_austria'       => 'info',
                        'sendgrid'         => 'warning',
                        'sophos'           => 'danger',
                        'proofpoint'       => 'danger',
                        'barracuda'        => 'danger',
                        'mimecast'         => 'danger',
                        'trendmicro'       => 'danger',
                        'cisco_ironport'   => 'danger',
                        'ovh'              => 'success',
                        'cloudflare_routing' => 'warning',
                        'mailbox_org'      => 'success',
                        default            => 'gray',
                    }),

                TextColumn::make('provider_label')
                    ->label('Provider Name')
                    ->limit(30),

                TextColumn::make('confidence')
                    ->label('Conf')
                    ->formatStateUsing(fn ($state) => round($state * 100) . '%')
                    ->sortable(),

                TextColumn::make('mx_records')
                    ->label('Primary MX')
                    ->formatStateUsing(function ($state) {
                        $mx = is_string($state) ? json_decode($state, true) : $state;
                        return $mx[0]['host'] ?? '-';
                    })
                    ->limit(30),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'verified' => 'success',
                        'failed'   => 'danger',
                        'pending'  => 'warning',
                        default    => 'gray',
                    }),

                TextColumn::make('smtp_reachable')
                    ->label('SMTP')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        true  => '✅ Reachable',
                        false => '❌ Unreachable',
                        null  => '-',
                    }),

                TextColumn::make('smtp_error')
                    ->label('Reason')
                    ->limit(25),

                TextColumn::make('verification_time_ms')
                    ->label('Time')
                    ->formatStateUsing(fn ($state) => $state ? $state . 'ms' : '-')
                    ->sortable(),

                TextColumn::make('source')
                    ->label('Source')
                    ->limit(15),

                TextColumn::make('created_at')
                    ->label('Verified')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('batch_id')
                    ->label('Batch')
                    ->options(fn () => EmailVerification::select('batch_id')
                        ->distinct()
                        ->limit(20)
                        ->pluck('batch_id', 'batch_id')
                    ),

                SelectFilter::make('provider')
                    ->label('Provider')
                    ->searchable()
                    ->options(fn () => EmailVerification::select('provider', 'provider_label')
                        ->distinct()
                        ->orderBy('provider')
                        ->get()
                        ->pluck('provider_label', 'provider')
                        ->toArray()
                    ),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'verified' => 'Verified',
                        'failed'   => 'Failed',
                        'pending'  => 'Pending',
                    ]),

                SelectFilter::make('source')
                    ->label('Source')
                    ->options(fn () => EmailVerification::select('source')
                        ->distinct()
                        ->orderBy('source')
                        ->pluck('source', 'source')
                        ->toArray()
                    ),

                TernaryFilter::make('smtp_checked')
                    ->label('SMTP Checked'),

                TernaryFilter::make('smtp_reachable')
                    ->label('SMTP Reachable'),
            ])
            ->emptyStateIcon('heroicon-o-check-badge')
            ->emptyStateHeading('No email verifications yet')
            ->emptyStateDescription('Run email:verify-providers to start verifying email addresses.');
    }
}
