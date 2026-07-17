<?php

namespace App\Filament\Resources\EmailFilterRules\Pages;

use App\Enums\EmailCategory;
use App\Filament\Resources\EmailFilterRules\EmailFilterRuleResource;
use App\Models\EmailAccount;
use App\Models\EmailFilterRule;
use App\Services\RuleExportService;
use App\Services\RuleImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEmailFilterRules extends ListRecords
{
    protected static string $resource = EmailFilterRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importEuKeywords')
                ->label('EU invoice keywords')
                ->icon('heroicon-o-language')
                ->color('success')
                ->modalHeading('Import rules for invoices/payments — all European languages')
                ->modalDescription('Filtering rules with keywords will be created for invoice and payment emails in 18 European language groups. Existing rules with the same name are skipped.')
                ->modalWidth('lg')
                ->form([
                    Toggle::make('require_attachment')
                        ->label('Emails with attachments only')
                        ->default(true)
                        ->helperText('Recommended — real invoices almost always have a PDF attached.'),
                    Select::make('then_category')
                        ->label('Auto-assigned category')
                        ->options(EmailCategory::options())
                        ->default('transaction')
                        ->required(),
                    CheckboxList::make('groups')
                        ->label('Language groups to import')
                        ->columns(2)
                        ->options(static::keywordGroups()->mapWithKeys(fn ($g) => [$g['key'] => $g['label']])->all())
                        ->default(static::keywordGroups()->pluck('key')->all()),
                ])
                ->modalSubmitActionLabel('Create selected rules')
                ->action(function (array $data): void {
                    $created  = 0;
                    $skipped  = 0;
                    $priority = (EmailFilterRule::max('priority') ?? 0) + 10;

                    foreach (static::keywordGroups() as $group) {
                        if (! in_array($group['key'], $data['groups'] ?? [], true)) {
                            continue;
                        }
                        $name = '🔍 EU Invoices — ' . $group['label'];
                        if (EmailFilterRule::where('name', $name)->exists()) {
                            $skipped++;
                            continue;
                        }
                        EmailFilterRule::create([
                            'name'               => $name,
                            'is_active'          => true,
                            'priority'           => $priority,
                            'match_type'         => 'all',
                            'subject_regex'      => $group['subject_regex'],
                            'require_attachment' => $data['require_attachment'],
                            'then_category'      => $data['then_category'],
                            'then_tag'           => 'invoice-eu',
                            'stop_processing'    => false,
                        ]);
                        $priority += 10;
                        $created++;
                    }

                    $msg = "{$created} rules created";
                    if ($skipped) {
                        $msg .= ", {$skipped} skipped (already exist)";
                    }
                    Notification::make()->title($msg)->success()->send();
                }),
            Action::make('export')
                ->label('Export rules')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    $service = app(RuleExportService::class);
                    $rules = \App\Models\EmailFilterRule::all();
                    $json = $service->exportToJson($rules);

                    return response()->streamDownload(
                        function () use ($json) {
                            echo $json;
                        },
                        'email-filter-rules-' . now()->format('Y-m-d-His') . '.json',
                        headers: ['Content-Type' => 'application/json']
                    );
                }),
            Action::make('import')
                ->label('Import rules')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form([
                    Select::make('email_account_id')
                        ->label('Email account')
                        ->required()
                        ->options(fn () => EmailAccount::pluck('email', 'id'))
                        ->helperText('Rules will be associated with this account'),
                    FileUpload::make('file')
                        ->label('JSON file')
                        ->required()
                        ->acceptedFileTypes(['application/json'])
                        ->helperText('Upload a previously exported JSON file'),
                ])
                ->action(function (array $data) {
                    try {
                        $path = storage_path('app/' . $data['file']);
                        $json = file_get_contents($path);

                        $service = app(RuleImportService::class);
                        $result = $service->importFromJson($json, $data['email_account_id']);

                        if ($result['errors']) {
                            Notification::make()
                                ->title('Partial import')
                                ->body("Imported: {$result['created']}, Errors: " . count($result['errors']))
                                ->warning()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Import successful')
                                ->body("{$result['created']} rules have been imported")
                                ->success()
                                ->send();
                        }

                        $this->redirect($this->getResource()::getUrl('index'));
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Import failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    // ── Keyword groups ────────────────────────────────────────────────────────

    private static function keywordGroups(): \Illuminate\Support\Collection
    {
        return collect([
            [
                'key'   => 'ro',
                'label' => '🇷🇴 Română',
                'subject_regex' => '/factur[aă]|bon\s+fiscal|chitan[tț][aă]|plat[aă]\b|achitare|scaden[tț][aă]|proform[aă]|aviz\s+de\s+plat[aă]|ordin\s+de\s+plat[aă]|not[aă]\s+de\s+(debit|credit)|bilet\s+la\s+ordin|borderou|stat\s+de\s+plat[aă]/iu',
            ],
            [
                'key'   => 'en',
                'label' => '🇬🇧 English',
                'subject_regex' => '/\binvoice\b|\breceipt\b|\bstatement\b|purchase\s+order|pro.?forma|remittance|debit\s+note|credit\s+note|overdue|payslip|bank\s+statement|\bpayment\b|\bbill\b/iu',
            ],
            [
                'key'   => 'de',
                'label' => '🇩🇪 Deutsch',
                'subject_regex' => '/rechnung|zahlung\b|beleg\b|quittung|mahnung|zahlungserinnerung|proforma|kontoauszug|lastschrift|[üu]berweisung|gutschrift|kassenbon|mahnbescheid|abbuchung/iu',
            ],
            [
                'key'   => 'fr',
                'label' => '🇫🇷 Français',
                'subject_regex' => '/facture|paiement|r[eè]glement|re[cç]u\b|relev[eé]\s+de\s+compte|avis\s+de\s+paiement|note\s+de\s+cr[eé]dit|note\s+de\s+d[eé]bit|avoir\b|bon\s+de\s+commande|quittance|virement/iu',
            ],
            [
                'key'   => 'es',
                'label' => '🇪🇸 Español',
                'subject_regex' => '/factura|pago\b|recibo\b|cobro\b|extracto|vencimiento|albar[aá]n|liquidaci[oó]n|nota\s+de\s+cr[eé]dito|nota\s+de\s+d[eé]bito|abono\b|comprobante/iu',
            ],
            [
                'key'   => 'pt',
                'label' => '🇵🇹 Português',
                'subject_regex' => '/fatura\b|pagamento|recibo\b|extrato\b|vencimento|boleto\b|nota\s+fiscal|comprovante|aviso\s+de\s+cobran[cç]a|duplicata/iu',
            ],
            [
                'key'   => 'it',
                'label' => '🇮🇹 Italiano',
                'subject_regex' => '/fattura|ricevuta|scontrino|estratto\s+conto|scadenza\b|bonifico|nota\s+di\s+credito|nota\s+di\s+debito|avviso\s+di\s+pagamento|quietanza/iu',
            ],
            [
                'key'   => 'nl',
                'label' => '🇳🇱 Nederlands',
                'subject_regex' => '/factuur|betaling\b|rekening\b|kwitantie|afschrift\b|betalingsherinnering|creditnota|debetnota|incasso\b|overschrijving|acceptgiro/iu',
            ],
            [
                'key'   => 'scan',
                'label' => '🇸🇪🇳🇴🇩🇰 Skandinavisk',
                'subject_regex' => '/faktura|betalning\b|betaling\b|kvitto\b|kvittering|kontoutdrag|kontoudtog|betalningsp[åa]minnelse|regning\b|kreditnota|debitnota|girering|bankutdrag/iu',
            ],
            [
                'key'   => 'pl',
                'label' => '🇵🇱 Polski',
                'subject_regex' => '/faktura|p[łl]atno[śs][cć]|rachunek|paragon\b|wyci[ąa]g|nota\s+ksi[ęe]gowa|polecenie\s+zap[łl]aty|przelew\b|nota\s+kredytowa|dow[oó]d\s+wp[łl]aty/iu',
            ],
            [
                'key'   => 'cs_sk',
                'label' => '🇨🇿🇸🇰 Čeština / Slovenčina',
                'subject_regex' => '/faktura|platba\b|[úu][cč]tenka|v[ýy]pis\b|da[nň]ov[ýy]\s+doklad|upom[ií]nka|dobropis|z[áa]loha\b/iu',
            ],
            [
                'key'   => 'hu',
                'label' => '🇭🇺 Magyar',
                'subject_regex' => '/sz[áa]mla|fizet[eé]s|nyugta\b|kivonat\b|bankki[vV]onat|j[oó]v[áa][iI]r[áa]s|terhe[lL][eé]s|[áa]tut[aA]l[áa]s/iu',
            ],
            [
                'key'   => 'south_slavic',
                'label' => '🇭🇷🇸🇮🇷🇸🇧🇦 South Slavic',
                'subject_regex' => '/ra[cč]un\b|pla[cć]anje|pla[cč]ilo|potvrda\b|faktura|avans\b|izvod\b|priznanica|nalog\s+za\s+pla[cć]anje/iu',
            ],
            [
                'key'   => 'fi',
                'label' => '🇫🇮 Suomi',
                'subject_regex' => '/lasku\b|maksu\b|kuitti\b|tiliote\b|laskutus|maksumuistutus|suoraveloitus|hyvityslasku|tilisiirto/iu',
            ],
            [
                'key'   => 'baltic',
                'label' => '🇪🇪🇱🇻🇱🇹 Baltijas / Baltic',
                'subject_regex' => '/arve\b|r[eē][kķ][iī]ns|s[aā]skaita|mok[eė]jimas|kviitung|maksejooks|maks[aā]jums|s[aā]skaita\s+fakt[uū]ra/iu',
            ],
            [
                'key'   => 'el',
                'label' => '🇬🇷 Ελληνικά',
                'subject_regex' => '/τιμολ[οό]γιο|απ[οό]δειξη|πληρωμ[ήη]|λογαριασμ[οό]ς|εξ[οό]φληση|timologio|apodeixi|pliromi/iu',
            ],
            [
                'key'   => 'bg',
                'label' => '🇧🇬 България (Cyrillic)',
                'subject_regex' => '/фактура|плащане|разписка|извлечение|платежен|сметка\b|квитанция|нареждане\s+за\s+плащане/iu',
            ],
            [
                'key'   => 'other_eu',
                'label' => '🇲🇹🇮🇪 Malti / Gaeilge',
                'subject_regex' => '/\bħlas\b|ri[cċ]evuta|sonrasc|[íi]oca[íi]ocht|adm[háa]il\b|nota\s+tal[- ]kredit/iu',
            ],
        ]);
    }
}
