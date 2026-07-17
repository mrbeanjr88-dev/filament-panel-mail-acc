<?php

namespace App\Filament\Resources\EmailFilterRules;

use App\Filament\Resources\EmailFilterRules\Pages\CreateEmailFilterRule;
use App\Filament\Resources\EmailFilterRules\Pages\EditEmailFilterRule;
use App\Filament\Resources\EmailFilterRules\Pages\ListEmailFilterRules;
use App\Filament\Resources\EmailFilterRules\Schemas\EmailFilterRuleForm;
use App\Filament\Resources\EmailFilterRules\Tables\EmailFilterRulesTable;
use App\Models\EmailFilterRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class EmailFilterRuleResource extends Resource
{
    protected static ?string $model = EmailFilterRule::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-funnel';
    protected static string|UnitEnum|null $navigationGroup = 'Email Customs';
    protected static ?string $navigationLabel = 'Filter Rules';
    protected static ?string $modelLabel = 'rule';
    protected static ?string $pluralModelLabel = 'filter rules';
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'from_contains', 'subject_contains'];
    }

    public static function form(Schema $schema): Schema
    {
        return EmailFilterRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailFilterRulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEmailFilterRules::route('/'),
            'create' => CreateEmailFilterRule::route('/create'),
            'edit'   => EditEmailFilterRule::route('/{record}/edit'),
        ];
    }
}
