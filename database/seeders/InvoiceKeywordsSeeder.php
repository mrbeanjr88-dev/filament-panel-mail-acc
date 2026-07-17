<?php

namespace Database\Seeders;

use App\Models\EmailFilterRule;
use Illuminate\Database\Seeder;

class InvoiceKeywordsSeeder extends Seeder
{
    public function run(): void
    {
        $priority = (EmailFilterRule::max('priority') ?? 0) + 10;

        foreach (static::rules() as $rule) {
            $exists = EmailFilterRule::where('name', $rule['name'])->first();

            if ($exists) {
                // Update regex/settings but keep any user-customised priority
                $exists->update([
                    'subject_regex'      => $rule['subject_regex'],
                    'match_type'         => $rule['match_type'],
                    'require_attachment' => $rule['require_attachment'],
                    'then_category'      => $rule['then_category'],
                    'then_tag'           => $rule['then_tag'],
                    'stop_processing'    => $rule['stop_processing'],
                    'is_active'          => $rule['is_active'],
                ]);
                continue;
            }

            EmailFilterRule::create(array_merge($rule, ['priority' => $priority]));
            $priority += 10;
        }

        $count = count(static::rules());
        $this->command->info("InvoiceKeywordsSeeder: {$count} reguli sincronizate.");
    }

    // ── Rule definitions ──────────────────────────────────────────────────────

    public static function rules(): array
    {
        $base = [
            'match_type'         => 'all',
            'require_attachment' => true,
            'then_category'      => 'transaction',
            'then_tag'           => 'factura-eu',
            'stop_processing'    => false,
            'is_active'          => true,
        ];

        return [
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇷🇴 Română',
                'subject_regex' => '/factur[aă]|bon\s+fiscal|chitan[tț][aă]|plat[aă]\b|achitare|scaden[tț][aă]|proform[aă]|aviz\s+de\s+plat[aă]|ordin\s+de\s+plat[aă]|not[aă]\s+de\s+(debit|credit)|bilet\s+la\s+ordin|borderou|stat\s+de\s+plat[aă]/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇬🇧 English',
                'subject_regex' => '/\binvoice\b|\breceipt\b|\bstatement\b|purchase\s+order|pro.?forma|remittance|debit\s+note|credit\s+note|overdue|payslip|bank\s+statement|\bpayment\b|\bbill\b/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇩🇪 Deutsch',
                'subject_regex' => '/rechnung|zahlung\b|beleg\b|quittung|mahnung|zahlungserinnerung|proforma|kontoauszug|lastschrift|[üu]berweisung|gutschrift|kassenbon|mahnbescheid|abbuchung/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇫🇷 Français',
                'subject_regex' => '/facture|paiement|r[eè]glement|re[cç]u\b|relev[eé]\s+de\s+compte|avis\s+de\s+paiement|note\s+de\s+cr[eé]dit|note\s+de\s+d[eé]bit|avoir\b|bon\s+de\s+commande|quittance|virement/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇪🇸 Español',
                'subject_regex' => '/factura|pago\b|recibo\b|cobro\b|extracto|vencimiento|albar[aá]n|liquidaci[oó]n|nota\s+de\s+cr[eé]dito|nota\s+de\s+d[eé]bito|abono\b|comprobante/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇵🇹 Português',
                'subject_regex' => '/fatura\b|pagamento|recibo\b|extrato\b|vencimento|boleto\b|nota\s+fiscal|comprovante|aviso\s+de\s+cobran[cç]a|duplicata/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇮🇹 Italiano',
                'subject_regex' => '/fattura|ricevuta|scontrino|estratto\s+conto|scadenza\b|bonifico|nota\s+di\s+credito|nota\s+di\s+debito|avviso\s+di\s+pagamento|quietanza/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇳🇱 Nederlands',
                'subject_regex' => '/factuur|betaling\b|rekening\b|kwitantie|afschrift\b|betalingsherinnering|creditnota|debetnota|incasso\b|overschrijving|acceptgiro/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇸🇪🇳🇴🇩🇰 Skandinavisk',
                'subject_regex' => '/faktura|betalning\b|betaling\b|kvitto\b|kvittering|kontoutdrag|kontoudtog|betalningsp[åa]minnelse|regning\b|kreditnota|debitnota|girering|bankutdrag/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇵🇱 Polski',
                'subject_regex' => '/faktura|p[łl]atno[śs][cć]|rachunek|paragon\b|wyci[ąa]g|nota\s+ksi[ęe]gowa|polecenie\s+zap[łl]aty|przelew\b|nota\s+kredytowa|dow[oó]d\s+wp[łl]aty/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇨🇿🇸🇰 Čeština / Slovenčina',
                'subject_regex' => '/faktura|platba\b|[úu][cč]tenka|v[ýy]pis\b|da[nň]ov[ýy]\s+doklad|upom[ií]nka|dobropis|z[áa]loha\b/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇭🇺 Magyar',
                'subject_regex' => '/sz[áa]mla|fizet[eé]s|nyugta\b|kivonat\b|bankki[vV]onat|j[oó]v[áa][iI]r[áa]s|terhe[lL][eé]s|[áa]tut[aA]l[áa]s/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇭🇷🇸🇮🇷🇸🇧🇦 South Slavic',
                'subject_regex' => '/ra[cč]un\b|pla[cć]anje|pla[cč]ilo|potvrda\b|faktura|avans\b|izvod\b|priznanica|nalog\s+za\s+pla[cć]anje/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇫🇮 Suomi',
                'subject_regex' => '/lasku\b|maksu\b|kuitti\b|tiliote\b|laskutus|maksumuistutus|suoraveloitus|hyvityslasku|tilisiirto/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇪🇪🇱🇻🇱🇹 Baltijas / Baltic',
                'subject_regex' => '/arve\b|r[eē][kķ][iī]ns|s[aā]skaita|mok[eė]jimas|kviitung|maksejooks|maks[aā]jums|s[aā]skaita\s+fakt[uū]ra/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇬🇷 Ελληνικά',
                'subject_regex' => '/τιμολ[οό]γιο|απ[οό]δειξη|πληρωμ[ήη]|λογαριασμ[οό]ς|εξ[οό]φληση|timologio|apodeixi|pliromi/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇧🇬 България (Cyrillic)',
                'subject_regex' => '/фактура|плащане|разписка|извлечение|платежен|сметка\b|квитанция|нареждане\s+за\s+плащане/iu',
            ]),
            array_merge($base, [
                'name'          => '🔍 Facturi EU — 🇲🇹🇮🇪 Malti / Gaeilge',
                'subject_regex' => '/\bħlas\b|ri[cċ]evuta|sonrasc|[íi]oca[íi]ocht|adm[háa]il\b|nota\s+tal[- ]kredit/iu',
            ]),
        ];
    }
}
