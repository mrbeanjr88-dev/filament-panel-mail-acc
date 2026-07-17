<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bank Account')
                ->icon('heroicon-o-building-library')
                ->columns(2)
                ->schema([
                    TextInput::make('label')
                        ->label('Label')->required()->placeholder('BT Current Account RON'),
                    TextInput::make('bank_name')->label('Bank')->placeholder('Banca Transilvania'),
                    TextInput::make('account_holder')->label('Account Holder')->required(),
                    Select::make('currency')
                        ->label('Currency')
                        ->options(['RON' => 'RON', 'EUR' => 'EUR', 'USD' => 'USD', 'GBP' => 'GBP'])
                        ->default('RON')->required(),
                    TextInput::make('iban')->label('IBAN')->placeholder('RO49AAAA1B31007593840000')
                        ->regex('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/')
                        ->columnSpanFull(),
                    TextInput::make('bic')->label('BIC/SWIFT')->minLength(8)->maxLength(11)->alphaNum(),
                    TextInput::make('account_number')->label('Account Number'),
                ]),

            Section::make('Links & Status')
                ->icon('heroicon-o-link')
                ->columns(2)
                ->schema([
                    Select::make('email_account_id')
                        ->label('Associated email account')
                        ->relationship('emailAccount', 'name')
                        ->searchable()->preload()
                        ->getOptionLabelUsing(fn ($value) => \App\Models\EmailAccount::find($value)?->name . ' (' . \App\Models\EmailAccount::find($value)?->email . ')'),
                    Toggle::make('is_active')->label('Active')->default(true),
                    Textarea::make('notes')->label('Notes')->columnSpanFull()->rows(2),
                ]),
        ]);
    }
}
