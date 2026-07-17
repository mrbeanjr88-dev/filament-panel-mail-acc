<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $title = 'Application Settings';
    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(AppSetting::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('⚠ Mandatory Quarantine')
                    ->description('ALL incoming emails are automatically moved to the Hold folder and kept there until each invoice is manually reviewed and approved. This behavior cannot be disabled.')
                    ->schema([]),

                Section::make('Mail Capture')
                    ->columns(2)
                    ->schema([
                        Select::make('capture_mode')
                            ->label('What to capture from Inbox')
                            ->options([
                                'all'    => 'ALL emails (recommended — no email is missed)',
                                'unseen' => 'Unread only (risk: emails already opened in another client are ignored)',
                            ])
                            ->required(),
                        Toggle::make('mark_as_read')
                            ->label('Mark as read if move to Hold fails')
                            ->helperText('Safety fallback when IMAP does not allow moving to Hold.'),
                        Toggle::make('auto_apply_rules')
                            ->label('Automatically apply filtering rules (metadata: category, tag, account)')
                            ->helperText('Rules only set metadata. Auto-approval via rules is permanently disabled.'),
                        Toggle::make('extract_bank_data')
                            ->label('Automatically extract invoice data (number, date, issuer, VAT, amount)'),
                    ]),

                Section::make('Rendering & Security')
                    ->columns(2)
                    ->schema([
                        Toggle::make('sanitize_html')->label('Sanitize HTML on display'),
                        Select::make('attachments_disk')
                            ->label('Attachments disk')
                            ->options(['local' => 'Local', 's3' => 'Amazon S3', 'public' => 'Public'])
                            ->default('local')
                            ->required(),
                    ]),

                Section::make('Default Values')
                    ->columns(3)
                    ->schema([
                        Select::make('default_currency')
                            ->label('Default currency')
                            ->options(['RON' => 'RON', 'EUR' => 'EUR', 'USD' => 'USD', 'GBP' => 'GBP']),
                        TextInput::make('default_hold_folder')->label('Default Hold folder'),
                        TextInput::make('default_fetch_limit')->label('Email limit per run')->numeric(),
                    ]),

                Section::make('PDF Processing')
                    ->description('Settings for automatically intercepting and modifying PDF attachments received via email.')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        Toggle::make('intercept_pdf_attachments')
                            ->label('Automatically intercept PDF attachments')
                            ->helperText('When enabled, received PDFs are held and can be modified before approval.'),
                        Toggle::make('auto_apply_pdf_templates')
                            ->label('Automatically apply PDF templates on sync')
                            ->helperText('Templates with auto_apply enabled are applied immediately upon capture.'),
                        TextInput::make('pdf_watermark_text')
                            ->label('Default watermark text')
                            ->default('CONFIDENTIAL')
                            ->helperText('Text applied as watermark on PDFs (alongside templates).'),
                        TextInput::make('pdf_stamp_text')
                            ->label('Default stamp text')
                            ->default('PROCESSED')
                            ->helperText('Text of the stamp applied to PDFs.'),
                        TextInput::make('pdf_stamp_operator')
                            ->label('Stamp operator name')
                            ->placeholder('e.g. John Smith')
                            ->helperText('Operator name displayed on the stamp.'),
                        TextInput::make('pdf_output_folder')
                            ->label('Modified PDF output folder')
                            ->default('email-customs/pdf')
                            ->helperText('Disk path where modified PDFs are saved.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        AppSetting::current()->update($this->form->getState());

        Notification::make()->title('Settings saved')->success()->send();
    }
}
