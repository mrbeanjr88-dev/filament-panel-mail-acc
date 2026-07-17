<?php

namespace App\Filament\Resources\EmailAccounts;

use App\Filament\Resources\EmailAccounts\Pages\CreateEmailAccount;
use App\Filament\Resources\EmailAccounts\Pages\EditEmailAccount;
use App\Filament\Resources\EmailAccounts\Pages\ListEmailAccounts;
use App\Filament\Resources\EmailAccounts\Schemas\EmailAccountForm;
use App\Filament\Resources\EmailAccounts\Tables\EmailAccountsTable;
use App\Models\EmailAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class EmailAccountResource extends Resource
{
    protected static ?string $model = EmailAccount::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-at-symbol';
    protected static string|UnitEnum|null $navigationGroup = 'Email Customs';
    protected static ?string $navigationLabel = 'Email Accounts';
    protected static ?string $modelLabel = 'email account';
    protected static ?string $pluralModelLabel = 'email accounts';
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $errors = \App\Models\EmailAccount::whereNotNull('last_error')->count();
        return $errors > 0 ? (string) $errors : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'host'];
    }

    public static function form(Schema $schema): Schema
    {
        return EmailAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEmailAccounts::route('/'),
            'create' => CreateEmailAccount::route('/create'),
            'edit'   => EditEmailAccount::route('/{record}/edit'),
        ];
    }
}
