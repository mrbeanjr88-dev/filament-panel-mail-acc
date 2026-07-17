<?php

namespace App\Filament\Resources\EmailVerifications\Pages;

use App\Models\EmailVerification;
use Filament\Resources\Pages\ListRecords;

class ListEmailVerifications extends ListRecords
{
    protected static string $resource = \App\Filament\Resources\EmailVerifications\EmailVerificationResource::class;

    protected static ?string $title = 'Email Verifications';
}
