<?php

namespace App\Services;

use App\Models\EmailFilterRule;
use Illuminate\Support\Facades\DB;

class RuleImportService
{
    public function importFromJson(string $json, int $emailAccountId): array
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException('Invalid JSON format: ' . $e->getMessage());
        }

        if (! is_array($data)) {
            throw new \InvalidArgumentException('JSON must contain an array of rules');
        }

        $results = [
            'created' => 0,
            'errors' => [],
        ];

        return DB::transaction(function () use ($data, $emailAccountId, $results) {
            foreach ($data as $index => $rule) {
                try {
                    $this->validateRuleData($rule);

                    EmailFilterRule::create([
                        'email_account_id' => $emailAccountId,
                        'name' => $rule['name'],
                        'priority' => $rule['priority'] ?? 100,
                        'is_active' => $rule['is_active'] ?? true,
                        'match_type' => $rule['match_type'] ?? 'all',
                        'from_contains' => $rule['conditions']['from_contains'] ?? null,
                        'from_regex' => $rule['conditions']['from_regex'] ?? null,
                        'subject_contains' => $rule['conditions']['subject_contains'] ?? null,
                        'subject_regex' => $rule['conditions']['subject_regex'] ?? null,
                        'to_contains' => $rule['conditions']['to_contains'] ?? null,
                        'body_contains' => $rule['conditions']['body_contains'] ?? null,
                        'require_attachment' => $rule['conditions']['require_attachment'] ?? null,
                        'amount_min' => $rule['conditions']['amount_min'] ?? null,
                        'amount_max' => $rule['conditions']['amount_max'] ?? null,
                        'then_bank_account_id' => $rule['actions']['then_bank_account_id'] ?? null,
                        'then_category' => $rule['actions']['then_category'] ?? null,
                        'then_tag' => $rule['actions']['then_tag'] ?? null,
                        'then_target_folder' => $rule['actions']['then_target_folder'] ?? null,
                        'then_auto_approve' => $rule['actions']['then_auto_approve'] ?? false,
                        'then_reject' => $rule['actions']['then_reject'] ?? false,
                        'stop_processing' => $rule['actions']['stop_processing'] ?? false,
                    ]);

                    $results['created']++;
                } catch (\Throwable $e) {
                    $results['errors'][] = [
                        'row' => $index + 1,
                        'rule' => $rule['name'] ?? 'Unknown',
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    private function validateRuleData(array $rule): void
    {
        if (empty($rule['name'])) {
            throw new \InvalidArgumentException('Rule must have a name');
        }

        if (! isset($rule['conditions']) || ! is_array($rule['conditions'])) {
            throw new \InvalidArgumentException('Rule must have a conditions array');
        }

        if (! isset($rule['actions']) || ! is_array($rule['actions'])) {
            throw new \InvalidArgumentException('Rule must have an actions array');
        }
    }
}
