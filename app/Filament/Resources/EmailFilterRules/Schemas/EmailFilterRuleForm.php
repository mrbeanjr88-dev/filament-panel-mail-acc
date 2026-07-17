<?php

namespace App\Filament\Resources\EmailFilterRules\Schemas;

use App\Enums\EmailCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailFilterRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rule')
                ->icon('heroicon-o-tag')
                ->columns(3)
                ->schema([
                    TextInput::make('name')->label('Name')->required()->columnSpan(2),
                    TextInput::make('priority')->label('Priority')->numeric()->default(0)
                        ->minValue(0)->step(10)->helperText('Lower priority = evaluated first'),
                    Toggle::make('is_active')->label('Active')->default(true),
                    Select::make('match_type')
                        ->label('Match')
                        ->options(['all' => 'ALL conditions', 'any' => 'ANY condition'])
                        ->default('all')->required(),
                    Select::make('email_account_id')
                        ->label('Only for account')
                        ->relationship('emailAccount', 'name')
                        ->placeholder('All accounts')->searchable(),
                ]),

            Section::make('Conditions')
                ->icon('heroicon-o-adjustments-horizontal')
                ->description('Leave empty what you don\'t want to check. "Contains" fields are case-insensitive.')
                ->columns(2)
                ->schema([
                    TextInput::make('from_contains')->label('Sender contains')->placeholder('@bancatransilvania.ro'),
                    TextInput::make('from_regex')->label('Sender (regex)')->placeholder('/no-?reply@.*bt\\.ro/i'),
                    TextInput::make('subject_contains')->label('Subject contains')->placeholder('transaction'),
                    TextInput::make('subject_regex')->label('Subject (regex)'),
                    TextInput::make('to_contains')->label('Recipient contains'),
                    TextInput::make('body_contains')->label('Body contains'),
                    Select::make('require_attachment')
                        ->label('Attachment')
                        ->options([1 => 'With attachment only', 0 => 'Without attachment only'])
                        ->placeholder('Any'),
                    TextInput::make('amount_min')->label('Min. amount')->numeric()->prefix('RON'),
                    TextInput::make('amount_max')->label('Max. amount')->numeric()->prefix('RON'),
                ]),

            Section::make('Actions on match')
                ->icon('heroicon-o-bolt')
                ->description('Actions are applied automatically when an email matches the rule. Auto-approval is permanently disabled — all invoices require manual review.')
                ->columns(2)
                ->schema([
                    Select::make('then_bank_account_id')
                        ->label('Assign bank account')
                        ->relationship('bankAccount', 'label')
                        ->searchable()->preload(),
                    Select::make('then_category')
                        ->label('Set category')
                        ->options(EmailCategory::options()),
                    TextInput::make('then_tag')->label('Tag')->placeholder('e.g. invoice, import'),
                    TextInput::make('then_target_folder')
                        ->label('Target folder on approval')
                        ->placeholder('e.g. INBOX/Approved')
                        ->helperText('Where the email lands after manual approval. Default: the account\'s Inbox folder.'),
                    Toggle::make('then_reject')
                        ->label('Auto-reject (spam/unwanted)')
                        ->default(false)
                        ->helperText('Email is automatically rejected without human intervention. Use only for obvious spam.'),
                    Toggle::make('stop_processing')
                        ->label('Stop at this rule')
                        ->default(true)
                        ->helperText('First matching rule wins — rules with lower priority are ignored.'),
                ]),
        ]);
    }
}
