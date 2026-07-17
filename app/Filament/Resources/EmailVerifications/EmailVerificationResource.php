<?php

namespace App\Filament\Resources\EmailVerifications;

use App\Filament\Resources\EmailVerifications\Pages\ListEmailVerifications;
use App\Models\EmailVerification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use UnitEnum;

class EmailVerificationResource extends Resource
{
    protected static ?string $model = EmailVerification::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';
    protected static string|UnitEnum|null $navigationGroup = 'Email Customs';
    protected static ?string $navigationLabel = 'Email Verifications';
    protected static ?string $modelLabel = 'Email Verification';
    protected static ?string $pluralModelLabel = 'Email Verifications';
    protected static ?int $navigationSort = 8;

    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'domain', 'provider', 'provider_label'];
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\EmailVerifications\Tables\EmailVerificationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailVerifications::route('/'),
        ];
    }
}
