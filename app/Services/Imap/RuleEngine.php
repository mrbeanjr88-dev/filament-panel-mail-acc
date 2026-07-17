<?php

namespace App\Services\Imap;

use App\Models\EmailFilterRule;
use App\Models\PendingEmail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Motorul de filtrare: evaluează regulile active (în ordinea priorității) împotriva unui
 * PendingEmail și aplică acțiunile (cont bancar, categorie, tag, folder, auto-approve, reject).
 *
 * Logica de potrivire per regulă:
 *  - match_type = all  → TOATE condițiile setate trebuie să fie adevărate
 *  - match_type = any  → CEL PUȚIN una dintre condițiile setate e adevărată
 * Condițiile lăsate null sunt ignorate.
 */
class RuleEngine
{
    /** Cache de reguli per request. */
    private ?Collection $rules = null;

    /** Regex pattern cache untuk performa. */
    private array $regexCache = [];

    public function apply(PendingEmail $email): ?EmailFilterRule
    {
        $matchedFirst = null;

        foreach ($this->rules() as $rule) {
            // Restrânge la cont, dacă regula e legată de unul anume.
            if ($rule->email_account_id && $rule->email_account_id !== $email->email_account_id) {
                continue;
            }

            if (! $this->matches($rule, $email)) {
                continue;
            }

            $this->applyActions($rule, $email);
            $matchedFirst ??= $rule;

            if ($rule->stop_processing) {
                break;
            }
        }

        return $matchedFirst;
    }

    private function rules(): Collection
    {
        return $this->rules ??= EmailFilterRule::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('id')
            ->get();
    }

    private function matches(EmailFilterRule $rule, PendingEmail $email): bool
    {
        $conditions = [];

        $from = mb_strtolower(trim(($email->from_name ?? '') . ' ' . ($email->from_email ?? '')));
        $subject = mb_strtolower((string) $email->subject);
        $to = mb_strtolower(collect($email->to ?? [])->pluck('email')->filter()->join(' '));
        $body = mb_strtolower((string) ($email->body_text ?: strip_tags((string) $email->body_html)));

        if ($rule->from_contains !== null) {
            $conditions[] = str_contains($from, mb_strtolower($rule->from_contains));
        }
        if ($rule->from_regex !== null) {
            $conditions[] = $this->matchesRegex($rule->from_regex, $from, $rule->id, 'from_regex');
        }
        if ($rule->subject_contains !== null) {
            $conditions[] = str_contains($subject, mb_strtolower($rule->subject_contains));
        }
        if ($rule->subject_regex !== null) {
            $conditions[] = $this->matchesRegex($rule->subject_regex, $subject, $rule->id, 'subject_regex');
        }
        if ($rule->to_contains !== null) {
            $conditions[] = str_contains($to, mb_strtolower($rule->to_contains));
        }
        if ($rule->body_contains !== null) {
            $conditions[] = str_contains($body, mb_strtolower($rule->body_contains));
        }
        if ($rule->require_attachment !== null) {
            $conditions[] = $email->has_attachments === $rule->require_attachment;
        }
        if ($rule->amount_min !== null) {
            $conditions[] = $email->extracted_amount !== null && (float) $email->extracted_amount >= (float) $rule->amount_min;
        }
        if ($rule->amount_max !== null) {
            $conditions[] = $email->extracted_amount !== null && (float) $email->extracted_amount <= (float) $rule->amount_max;
        }

        if (empty($conditions)) {
            return false; // regulă fără condiții = nu potrivește nimic (evită „prinde tot" accidental)
        }

        return $rule->match_type === 'any'
            ? in_array(true, $conditions, true)
            : ! in_array(false, $conditions, true);
    }

    private function matchesRegex(string $pattern, string $text, int $ruleId, string $field): bool
    {
        $cacheKey = "{$ruleId}_{$field}";

        if (! isset($this->regexCache[$cacheKey])) {
            try {
                // Validate and normalize pattern
                $normalized = $this->normalizePattern($pattern);

                // Test pattern compiles and runs quickly
                $result = preg_match($normalized, '');
                if ($result === false) {
                    throw new \Exception(preg_last_error_msg());
                }

                $this->regexCache[$cacheKey] = $normalized;
            } catch (\Throwable $e) {
                Log::warning('Invalid regex in rule', [
                    'rule_id' => $ruleId,
                    'field' => $field,
                    'pattern' => $pattern,
                    'error' => $e->getMessage(),
                ]);
                return false; // Invalid regex fails silently (no match)
            }
        }

        $compiled = $this->regexCache[$cacheKey];
        return (bool) preg_match($compiled, $text);
    }

    private function normalizePattern(string $pattern): string
    {
        // Already in /pattern/flags format — detect and ensure u flag for UTF-8
        if (preg_match('~^/.*?/([imxsuU]*)$~s', $pattern, $m)) {
            return str_contains($m[1], 'u') ? $pattern : $pattern . 'u';
        }

        // Convert plain pattern to /pattern/iu (case-insensitive + UTF-8 Unicode)
        return '/' . str_replace('/', '\/', $pattern) . '/iu';
    }

    private function applyActions(EmailFilterRule $rule, PendingEmail $email): void
    {
        if ($rule->then_bank_account_id) {
            $email->bank_account_id = $rule->then_bank_account_id;
        }
        if ($rule->then_category) {
            $email->category = $rule->then_category;
        }
        if ($rule->then_tag) {
            $email->tag = $rule->then_tag;
        }
        if ($rule->then_target_folder) {
            $email->target_folder = $rule->then_target_folder;
        }

        $email->matched_rule_id = $rule->id;
    }
}
