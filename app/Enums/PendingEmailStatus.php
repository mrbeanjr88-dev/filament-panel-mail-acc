<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PendingEmailStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Processed = 'processed';
    case Rejected = 'rejected';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Processed => 'Processed',
            self::Rejected => 'Rejected',
            self::Failed => 'Failed',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'info',
            self::Processed => 'success',
            self::Rejected => 'gray',
            self::Failed => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check',
            self::Processed => 'heroicon-o-inbox-arrow-down',
            self::Rejected => 'heroicon-o-x-mark',
            self::Failed => 'heroicon-o-exclamation-triangle',
        };
    }
}
