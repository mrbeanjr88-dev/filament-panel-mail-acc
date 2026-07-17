<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum EmailCategory: string implements HasColor, HasIcon, HasLabel
{
    case Transaction = 'transaction';
    case Statement = 'statement';
    case Security = 'security';
    case Spam = 'spam';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Transaction => 'Transaction',
            self::Statement => 'Statement',
            self::Security => 'Security',
            self::Spam => 'Spam',
            self::Other => 'Other',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Transaction => 'heroicon-o-credit-card',
            self::Statement => 'heroicon-o-document-text',
            self::Security => 'heroicon-o-lock-closed',
            self::Spam => 'heroicon-o-no-symbol',
            self::Other => 'heroicon-o-ellipsis-horizontal-circle',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Transaction => 'success',
            self::Statement => 'info',
            self::Security => 'warning',
            self::Spam => 'danger',
            self::Other => 'gray',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($c) => [$c->value => $c->getLabel()])->all();
    }
}
