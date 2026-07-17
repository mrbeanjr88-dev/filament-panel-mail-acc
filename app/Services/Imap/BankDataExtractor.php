<?php

namespace App\Services\Imap;

/**
 * Extrage date financiare dintr-un corp de email:
 * - Tranzacții bancare: sumă, valută, sens (debit/credit)
 * - Facturi: număr factură, dată factură, scadență, emitent, TVA
 *
 * Returnează: ['amount', 'currency', 'direction', 'is_invoice',
 *              'invoice_number', 'invoice_date', 'due_date', 'invoice_issuer', 'vat_amount']
 * Toate câmpurile pot fi null — verificarea umană rămâne necesară.
 */
class BankDataExtractor
{
    private const CURRENCIES = ['RON', 'EUR', 'USD', 'GBP', 'CHF', 'LEI'];

    private const INVOICE_KEYWORDS = [
        'factură', 'factura', 'nr\.?\s*factură', 'nr\.?\s*factura',
        'invoice', 'bill', 'billing', 'facture',
        'invoice\s+no\.?', 'inv\.?\s*#?', 'bill\s+no\.?',
    ];

    public function extract(string $text): array
    {
        $text = $this->normalize($text);

        return [
            'amount'          => $this->amount($text),
            'currency'        => $this->currency($text),
            'direction'       => $this->direction($text),
            'is_invoice'      => $this->isInvoice($text),
            'invoice_number'  => $this->invoiceNumber($text),
            'invoice_date'    => $this->invoiceDate($text),
            'due_date'        => $this->dueDate($text),
            'invoice_issuer'  => $this->invoiceIssuer($text),
            'vat_amount'      => $this->vatAmount($text),
        ];
    }

    private function normalize(string $text): string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\x{00A0}/u', ' ', $text); // nbsp
        $text = preg_replace('/[ \t]+/', ' ', $text);

        return $text;
    }

    /** Suma tranzacției / totalul facturii. */
    private function amount(string $text): ?float
    {
        // Specific transaction patterns
        if (preg_match('/(?:suma|amount|valoare\s+totala|valoare\s+total[aă]|total\s+(?:de\s+plat[aă]|factur[aă]|invoice))[^\d]{0,25}([\d\s.,]+)/iu', $text, $m)) {
            $v = $this->toFloat($m[1]);
            if ($v !== null && $v > 0) return $v;
        }
        // Pattern: number followed by currency
        if (preg_match('/([\d]{1,3}(?:[.\s]\d{3})*(?:,\d{2})|\d+[.,]\d{2})\s*(?:' . implode('|', self::CURRENCIES) . ')/iu', $text, $m)) {
            return $this->toFloat($m[1]);
        }
        // Generic transac
        if (preg_match('/(?:tranzac\w+)[^\d]{0,20}([\d.,]+)/iu', $text, $m)) {
            return $this->toFloat($m[1]);
        }

        return null;
    }

    /** Valuta detectată. */
    private function currency(string $text): ?string
    {
        foreach (self::CURRENCIES as $c) {
            if (preg_match('/\b' . preg_quote($c, '/') . '\b/iu', $text)) {
                return strtoupper($c === 'LEI' ? 'RON' : $c);
            }
        }

        return null;
    }

    /** Sens tranzacție: debit sau credit. */
    private function direction(string $text): ?string
    {
        if (preg_match('/\b(debit|debitare|pl[aă][tț]it[aă]?|retragere|cump[aă]r[aă]tur[aă]|transfer\s+c[aă]tre|cheltuial[aă])/iu', $text)) {
            return 'debit';
        }
        if (preg_match('/\b(credit|creditare|încasare|incasare|alimentare|depunere|primit[aă]?|transfer\s+de\s+la|plat[aă]\s+primit[aă])/iu', $text)) {
            return 'credit';
        }

        return null;
    }

    /** Numărul facturii (ex: FAC-2024-001, INV-0042, Seria RO Nr. 123). */
    private function invoiceNumber(string $text): ?string
    {
        $patterns = [
            // Romanian: "Factura nr. FAC-2024-001" or "Nr. factura: 123"
            '/(?:factur[aă]\s*(?:nr\.?|număr|numar|no\.?|#)?|nr\.?\s*factur[aă])[:\s]*([A-Z0-9][A-Z0-9\-\/]{1,30})/iu',
            // English: "Invoice No. INV-001" or "Invoice #123"
            '/(?:invoice\s*(?:no\.?|number|#|num\.?))[:\s]*([A-Z0-9][A-Z0-9\-\/]{1,30})/iu',
            // Serie + Nr: "Seria RO Nr. 1234"
            '/(?:seria\s+[A-Z]{1,5}\s+(?:nr\.?|număr)[:\s]*)(\d{1,10})/iu',
            // Standalone invoice codes: "FAC-2024-001" or "INV2024001"
            '/\b((?:FAC|INV|FACT|RO|BC|BT|FCT)[A-Z0-9]{0,5}[-\/]?\d{4}[-\/]?\d{1,6})\b/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $candidate = trim($m[1]);
                if (strlen($candidate) >= 2 && strlen($candidate) <= 50) {
                    return strtoupper($candidate);
                }
            }
        }

        return null;
    }

    /** Data emiterii facturii. */
    private function invoiceDate(string $text): ?string
    {
        $patterns = [
            // "Data facturii: 01.02.2024" or "Data emiterii: 2024-02-01"
            '/(?:data\s+(?:factur[aă]|facturii|emiterii|documentului))[:\s]*([\d]{1,2}[.\/\-][\d]{1,2}[.\/\-][\d]{2,4}|[\d]{4}[.\/\-][\d]{1,2}[.\/\-][\d]{1,2})/iu',
            // "Invoice date: 01/02/2024"
            '/(?:invoice\s+date|date\s+of\s+invoice|issued\s+(?:on|date))[:\s]*([\d]{1,2}[.\/\-][\d]{1,2}[.\/\-][\d]{2,4}|[\d]{4}[.\/\-][\d]{1,2}[.\/\-][\d]{1,2})/iu',
            // "Emis în data de 15.03.2024"
            '/(?:emis\w*\s+(?:în\s+)?data\s+(?:de\s+)?)([\d]{1,2}[.\/\-][\d]{1,2}[.\/\-][\d]{2,4})/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return $this->normalizeDate($m[1]);
            }
        }

        return null;
    }

    /** Scadența (data limită de plată). */
    private function dueDate(string $text): ?string
    {
        $patterns = [
            '/(?:scaden[tț][aă]|termen\s+(?:de\s+)?plat[aă]|data\s+limit[aă])[:\s]*([\d]{1,2}[.\/\-][\d]{1,2}[.\/\-][\d]{2,4}|[\d]{4}[.\/\-][\d]{1,2}[.\/\-][\d]{1,2})/iu',
            '/(?:due\s+date|payment\s+due|due\s+by|pay\s+by)[:\s]*([\d]{1,2}[.\/\-][\d]{1,2}[.\/\-][\d]{2,4}|[\d]{4}[.\/\-][\d]{1,2}[.\/\-][\d]{1,2})/iu',
            '/(?:de\s+achitat\s+p[âa]n[aă]\s+(?:la\s+)?|termen\s+scaden[tț][aă][:\s]*)([\d]{1,2}[.\/\-][\d]{1,2}[.\/\-][\d]{2,4})/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return $this->normalizeDate($m[1]);
            }
        }

        return null;
    }

    /** Emitentul facturii (furnizor / vânzător). */
    private function invoiceIssuer(string $text): ?string
    {
        $patterns = [
            // "Furnizor: SC Firma SRL" or "Vânzător: ..."
            '/(?:furnizor|v[âa]nz[aă]tor|emitent|emis\s+de|from\s+company)[:\s]+([A-ZȘȚĂÎÂ][^\n\r,;]{3,60}(?:SRL|SA|RA|PFA|II|IF|SNC|SCS)?)/iu',
            // "Societatea SC FIRMA SRL" or just company name before CUI/CIF
            '/\b(S\.?C\.?\s+[A-ZȘȚĂÎÂ][A-Za-zȘȚĂÎÂșțăîâ\s&\-\.]{2,40}(?:S\.?R\.?L\.?|S\.?A\.?))\b/u',
            // "Billed by:" or "Issued by:"
            '/(?:billed\s+by|issued\s+by|seller|provider)[:\s]+([A-Z][^\n\r,;]{3,60})/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $name = trim(preg_replace('/\s+/', ' ', $m[1]));
                if (strlen($name) >= 3 && strlen($name) <= 100) {
                    return $name;
                }
            }
        }

        return null;
    }

    /** Valoarea TVA. */
    private function vatAmount(string $text): ?float
    {
        $patterns = [
            '/(?:TVA|T\.V\.A\.?|VAT)[:\s]*([\d.,]+)\s*(?:' . implode('|', self::CURRENCIES) . ')?/iu',
            '/(?:tax\s+amount|impozit)[:\s]*([\d.,]+)/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $v = $this->toFloat($m[1]);
                if ($v !== null && $v > 0) return $v;
            }
        }

        return null;
    }

    private function isInvoice(string $text): bool
    {
        $pattern = '/(' . implode('|', self::INVOICE_KEYWORDS) . ')/iu';

        return (bool) preg_match($pattern, $text);
    }

    /** Normalizează o dată din format dd.mm.yyyy / yyyy-mm-dd în Y-m-d pentru DB. */
    private function normalizeDate(string $raw): ?string
    {
        $raw = trim($raw);
        // yyyy-mm-dd or yyyy/mm/dd
        if (preg_match('/^(\d{4})[.\/-](\d{1,2})[.\/-](\d{1,2})$/', $raw, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        // dd.mm.yyyy or dd/mm/yyyy or dd-mm-yyyy
        if (preg_match('/^(\d{1,2})[.\/-](\d{1,2})[.\/-](\d{2,4})$/', $raw, $m)) {
            $year = strlen($m[3]) === 2 ? (int) $m[3] + 2000 : (int) $m[3];
            return sprintf('%04d-%02d-%02d', $year, $m[2], $m[1]);
        }

        return null;
    }

    /** Convertește „1.234,56" sau „1,234.56" sau „1234.56" în float. */
    private function toFloat(string $raw): ?float
    {
        $raw = trim(preg_replace('/\s/', '', $raw)); // remove spaces
        $hasComma = str_contains($raw, ',');
        $hasDot   = str_contains($raw, '.');

        if ($hasComma && $hasDot) {
            if (strrpos($raw, ',') > strrpos($raw, '.')) {
                $raw = str_replace('.', '', $raw);
                $raw = str_replace(',', '.', $raw);
            } else {
                $raw = str_replace(',', '', $raw);
            }
        } elseif ($hasComma) {
            $raw = str_replace(',', '.', $raw);
        }

        $raw = preg_replace('/[^\d.\-]/', '', $raw);

        return is_numeric($raw) ? (float) $raw : null;
    }
}
