<?php

namespace App\Filament\Resources\PendingEmails\Schemas;

use App\Models\PendingEmail;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PendingEmailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Header')
                ->columns(2)
                ->schema([
                    TextEntry::make('from_email')->label('From')
                        ->formatStateUsing(fn ($state, PendingEmail $r) => trim(($r->from_name ?? '') . ' <' . $state . '>')),
                    TextEntry::make('date')->label('Date')->dateTime('d.m.Y H:i'),
                    TextEntry::make('subject')->label('Subject')->columnSpanFull(),
                    TextEntry::make('to')->label('To')
                        ->formatStateUsing(fn ($state) => collect($state ?? [])->pluck('email')->filter()->join(', ')),
                    TextEntry::make('cc')
                        ->label('CC')
                        ->formatStateUsing(fn ($state) => collect($state ?? [])->pluck('email')->filter()->join(', ') ?: '—')
                        ->placeholder('—'),
                    TextEntry::make('emailAccount.name')->label('Account'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('approved_at')
                        ->label('Approved at')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('—')
                        ->visible(fn (PendingEmail $r) => filled($r->approved_at)),
                    TextEntry::make('approved_by')
                        ->label('Approved by')
                        ->placeholder('—')
                        ->visible(fn (PendingEmail $r) => filled($r->approved_by)),
                    TextEntry::make('rejected_at')
                        ->label('Rejected at')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('—')
                        ->visible(fn (PendingEmail $r) => filled($r->rejected_at)),
                ]),

            Section::make('Classification & extracted data')
                ->columns(3)
                ->schema([
                    TextEntry::make('category')->label('Category')->badge()->placeholder('—'),
                    TextEntry::make('tag')->label('Tag')->badge()->placeholder('—'),
                    TextEntry::make('bankAccount.label')->label('Bank account')->placeholder('—'),
                    TextEntry::make('matchedRule.name')->label('Applied rule')->placeholder('—'),
                    TextEntry::make('extracted_amount')->label('Amount')
                        ->formatStateUsing(fn ($state, PendingEmail $r) => $state === null
                            ? '—' : number_format((float) $state, 2) . ' ' . ($r->extracted_currency ?? '')),
                    TextEntry::make('extracted_direction')
                        ->label('Direction')
                        ->badge()
                        ->color(fn ($state) => $state === 'debit' ? 'danger' : 'success')
                        ->formatStateUsing(fn ($state) => ucfirst($state ?? '—'))
                        ->placeholder('—'),
                ]),

            Section::make('Invoice data')
                ->columns(3)
                ->collapsible()
                ->schema([
                    TextEntry::make('invoice_number')->label('Invoice no.')->placeholder('—')->copyable(),
                    TextEntry::make('invoice_date')->label('Invoice date')->date('d.m.Y')->placeholder('—'),
                    TextEntry::make('due_date')->label('Due date')->date('d.m.Y')->placeholder('—'),
                    TextEntry::make('invoice_issuer')->label('Issuer')->placeholder('—'),
                    TextEntry::make('vat_amount')->label('VAT')
                        ->formatStateUsing(fn ($state, PendingEmail $r) => $state === null
                            ? '—' : number_format((float) $state, 2) . ' ' . ($r->extracted_currency ?? ''))
                        ->placeholder('—'),
                ])
                ->visible(fn (PendingEmail $r) => filled($r->invoice_number) || filled($r->invoice_date) || filled($r->invoice_issuer)),

            Section::make('PDF processing')
                ->columns(3)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('pdf_intercepted')
                        ->label('PDF intercepted')
                        ->badge()
                        ->color(fn ($state) => $state ? 'info' : 'gray')
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                    TextEntry::make('pdf_modified')
                        ->label('PDF modified')
                        ->badge()
                        ->color(fn ($state) => $state ? 'warning' : 'gray')
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                    TextEntry::make('pdf_pages_original')
                        ->label('Original pages')
                        ->placeholder('—'),
                    TextEntry::make('pdf_pages_modified')
                        ->label('Final pages')
                        ->placeholder('—'),
                    TextEntry::make('pdf_modification_summary')
                        ->label('PDF change history')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ])
                ->visible(fn (PendingEmail $r) => $r->pdf_intercepted || $r->pdf_modified),

            Section::make('Content')
                ->collapsed(true)
                ->collapsible()
                ->schema([
                    TextEntry::make('body')->hiddenLabel()->html()
                        ->state(fn (PendingEmail $r) => $r->safeHtml()),
                ]),

            Section::make('Notes & errors')
                ->schema([
                    TextEntry::make('notes')->label('Operator notes')->placeholder('—'),
                    TextEntry::make('last_error')->label('Last error')->color('danger')
                        ->visible(fn (PendingEmail $r) => filled($r->last_error)),
                ]),

            Section::make('Technical details')
                ->collapsed(true)
                ->collapsible()
                ->schema([
                    TextEntry::make('size')
                        ->label('Size')
                        ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 2) . ' KB' : '—')
                        ->placeholder('—'),
                    TextEntry::make('modified')
                        ->label('Modified')
                        ->badge()
                        ->color(fn ($state) => $state ? 'warning' : 'success')
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                    TextEntry::make('message_id')
                        ->label('Message ID')
                        ->placeholder('—')
                        ->copyable(),
                    TextEntry::make('hold_uid')
                        ->label('UID on hold')
                        ->placeholder('—'),
                    TextEntry::make('target_folder')
                        ->label('Destination folder')
                        ->placeholder('—'),
                ]),
        ]);
    }
}
