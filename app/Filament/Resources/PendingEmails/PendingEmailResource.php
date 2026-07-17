<?php

namespace App\Filament\Resources\PendingEmails;

use App\Enums\PendingEmailStatus;
use App\Filament\Resources\PendingEmails\Pages\ListPendingEmails;
use App\Filament\Resources\PendingEmails\Pages\ViewPendingEmail;
use App\Filament\Resources\PendingEmails\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\PendingEmails\Schemas\PendingEmailInfolist;
use App\Filament\Resources\PendingEmails\Tables\PendingEmailsTable;
use App\Models\PendingEmail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PendingEmailResource extends Resource
{
    protected static ?string $model = PendingEmail::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|UnitEnum|null $navigationGroup = 'Email Customs';
    protected static ?string $navigationLabel = 'Quarantine';
    protected static ?string $modelLabel = 'quarantined email';
    protected static ?string $pluralModelLabel = 'quarantined emails';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) PendingEmail::where('status', PendingEmailStatus::Pending)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Emails awaiting approval';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['subject', 'from_email', 'from_name', 'message_id'];
    }

    public static function infolist(Schema $schema): Schema
    {
        return PendingEmailInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PendingEmailsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AttachmentsRelationManager::class,
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPendingEmails::route('/'),
            'view'  => ViewPendingEmail::route('/{record}'),
        ];
    }
}
