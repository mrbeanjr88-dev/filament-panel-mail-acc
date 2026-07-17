<?php

namespace App\Filament\Resources\PdfTemplates\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PdfTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Template Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Template Name')
                        ->required()
                        ->placeholder('Watermark invoices BT'),
                    Select::make('type')
                        ->label('Operation Type')
                        ->options([
                            'watermark'       => 'Watermark (text on page)',
                            'stamp'           => 'Stamp (date + operator)',
                            'header_footer'   => 'Custom Header / Footer',
                            'merge'           => 'Overlay another PDF',
                            'redact'          => 'Redact zones (hide text)',
                            'extract'         => 'Extract pages',
                            'rotate'          => 'Rotate pages',
                            'flatten'         => 'Flatten (form -> text)',
                            'generate'        => 'Generate PDF from template',
                        ])
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set): void {
                            $set('config', static::defaultConfig($state));
                        }),
                    Select::make('email_account_id')
                        ->label('Only for account')
                        ->relationship('emailAccount', 'name')
                        ->placeholder('All accounts')
                        ->searchable()
                        ->columnSpan(2),
                    Toggle::make('is_active')->label('Active')->default(true),
                    Toggle::make('auto_apply')
                        ->label('Auto-apply on sync')
                        ->default(false)
                        ->helperText('Automatically applies when the email matches the criteria below.'),
                    TextInput::make('priority')
                        ->label('Priority')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower = evaluated first'),
                ]),

            Section::make('Matching Criteria')
                ->description('The template is applied only to emails that meet these conditions. Leave empty to apply to all.')
                ->icon('heroicon-o-funnel')
                ->columns(2)
                ->schema([
                    TextInput::make('match_criteria.from_contains')
                        ->label('Sender contains')
                        ->placeholder('@banca-transilvania.ro'),
                    TextInput::make('match_criteria.subject_contains')
                        ->label('Subject contains')
                        ->placeholder('factura'),
                    TextInput::make('match_criteria.body_contains')
                        ->label('Body contains'),
                    Toggle::make('match_criteria.require_pdf')
                        ->label('Only with PDF attachment')
                        ->default(true),
                ]),

            Section::make('Operation Configuration')
                ->description(fn (Get $get): string => match ($get('type')) {
                    'watermark'       => 'The watermark text is applied to each page at the specified angle.',
                    'stamp'           => 'The stamp contains the text, operator name, and current date.',
                    'header_footer'   => 'Text is added to the top/bottom of each page. Use {page} and {total} for page numbers.',
                    'merge'           => 'Another PDF (e.g., footer with company details) is overlaid on each page.',
                    'redact'          => 'Define the zones (x, y, width, height) that will be covered with white.',
                    'extract'         => 'Extract only the specified pages (e.g., 1-3,5,7).',
                    'rotate'          => 'Rotates all pages by the specified angle.',
                    'flatten'         => 'Converts interactive fields to flat text (simple operation).',
                    'generate'        => 'Generates a new PDF from an HTML/Blade template.',
                    default           => 'Configure the parameters for this operation.',
                })
                ->schema([
                    // Watermark params
                    TextInput::make('config.text')
                        ->label('Text')
                        ->default('CONFIDENTIAL')
                        ->visible(fn (Get $get) => in_array($get('type'), ['watermark'])),
                    TextInput::make('config.font_size')
                        ->label('Font size')
                        ->numeric()
                        ->default(60)
                        ->visible(fn (Get $get) => $get('type') === 'watermark'),
                    TextInput::make('config.color')
                        ->label('Color (hex)')
                        ->default('#CCCCCC')
                        ->visible(fn (Get $get) => $get('type') === 'watermark'),
                    TextInput::make('config.opacity')
                        ->label('Opacity (0-1)')
                        ->numeric()
                        ->default(0.3)
                        ->step(0.1)
                        ->visible(fn (Get $get) => $get('type') === 'watermark'),
                    TextInput::make('config.angle')
                        ->label('Angle (degrees)')
                        ->numeric()
                        ->default(-45)
                        ->visible(fn (Get $get) => $get('type') === 'watermark'),

                    // Stamp params
                    TextInput::make('config.text')
                        ->label('Stamp text')
                        ->default('PROCESSED')
                        ->visible(fn (Get $get) => $get('type') === 'stamp'),
                    TextInput::make('config.operator')
                        ->label('Operator')
                        ->visible(fn (Get $get) => $get('type') === 'stamp'),
                    Select::make('config.position')
                        ->label('Position')
                        ->options([
                            'top-left'     => 'Top left',
                            'top-right'    => 'Top right',
                            'bottom-left'  => 'Bottom left',
                            'bottom-right' => 'Bottom right',
                            'center'       => 'Center',
                        ])
                        ->default('bottom-right')
                        ->visible(fn (Get $get) => $get('type') === 'stamp'),
                    TextInput::make('config.date_format')
                        ->label('Date format')
                        ->default('d.m.Y H:i')
                        ->visible(fn (Get $get) => $get('type') === 'stamp'),

                    // Header/Footer params
                    TextInput::make('config.header_text')
                        ->label('Header text')
                        ->placeholder('Confidential — {page}/{total}')
                        ->visible(fn (Get $get) => $get('type') === 'header_footer'),
                    TextInput::make('config.footer_text')
                        ->label('Footer text')
                        ->placeholder('Auto-generated — {page}/{total}')
                        ->visible(fn (Get $get) => $get('type') === 'header_footer'),
                    TextInput::make('config.font_size')
                        ->label('Font size')
                        ->numeric()
                        ->default(8)
                        ->visible(fn (Get $get) => $get('type') === 'header_footer'),
                    TextInput::make('config.color')
                        ->label('Color (hex)')
                        ->default('#000000')
                        ->visible(fn (Get $get) => $get('type') === 'header_footer'),

                    // Extract params
                    TextInput::make('config.pages')
                        ->label('Pages to extract')
                        ->placeholder('1-3,5,7-9')
                        ->helperText('Format: 1,3,5 or 1-3,5,7-9')
                        ->visible(fn (Get $get) => $get('type') === 'extract'),

                    // Rotate params
                    Select::make('config.angle')
                        ->label('Rotation angle')
                        ->options([90 => '90°', 180 => '180°', 270 => '270°'])
                        ->default(90)
                        ->visible(fn (Get $get) => $get('type') === 'rotate'),

                    // Redact params
                    Repeater::make('config.zones')
                        ->label('Zones to redact')
                        ->schema([
                            TextInput::make('x')->label('X')->numeric()->default(0),
                            TextInput::make('y')->label('Y')->numeric()->default(0),
                            TextInput::make('width')->label('Width')->numeric()->default(100),
                            TextInput::make('height')->label('Height')->numeric()->default(20),
                            TextInput::make('page')->label('Page')->default('all')->placeholder('all | 1 | 2'),
                        ])
                        ->columns(5)
                        ->visible(fn (Get $get) => $get('type') === 'redact'),

                    // Merge params
                    TextInput::make('config.overlay_path')
                        ->label('Overlay PDF path on disk')
                        ->placeholder('email-customs/overlay-footer.pdf')
                        ->visible(fn (Get $get) => $get('type') === 'merge'),
                    Select::make('config.page')
                        ->label('On which pages')
                        ->options(['all' => 'All', 'first' => 'First', 'last' => 'Last'])
                        ->default('all')
                        ->visible(fn (Get $get) => $get('type') === 'merge'),

                    // Generate params
                    Textarea::make('config.html')
                        ->label('HTML / Blade template content')
                        ->rows(8)
                        ->placeholder('<h1>Invoice #{invoice_number}</h1><p>Date: {date}</p>')
                        ->visible(fn (Get $get) => $get('type') === 'generate'),
                ])
                ->columnSpanFull(),
        ]);
    }

    private static function defaultConfig(?string $type): array
    {
        return match ($type) {
            'watermark' => [
                'text'      => 'CONFIDENTIAL',
                'font_size' => 60,
                'color'     => '#CCCCCC',
                'opacity'   => 0.3,
                'angle'     => -45,
            ],
            'stamp' => [
                'text'        => 'PROCESSED',
                'operator'    => '',
                'position'    => 'bottom-right',
                'date_format' => 'd.m.Y H:i',
                'font_size'   => 12,
            ],
            'header_footer' => [
                'header_text' => '',
                'footer_text' => '',
                'font_size'   => 8,
                'color'       => '#000000',
            ],
            'extract' => ['pages' => ''],
            'rotate'  => ['angle' => 90],
            'redact'  => ['zones' => []],
            'merge'   => ['overlay_path' => '', 'page' => 'all', 'scale' => 1.0],
            'generate' => ['html' => ''],
            default => [],
        };
    }
}
