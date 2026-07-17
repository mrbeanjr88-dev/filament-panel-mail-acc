<?php

namespace App\Services;

use App\Models\EmailFilterRule;
use Illuminate\Support\Collection;

class RuleExportService
{
    public function exportToJson(Collection $rules): string
    {
        $data = $rules->map(fn ($rule) => [
            'name' => $rule->name,
            'priority' => $rule->priority,
            'is_active' => $rule->is_active,
            'match_type' => $rule->match_type,
            'conditions' => [
                'from_contains' => $rule->from_contains,
                'from_regex' => $rule->from_regex,
                'subject_contains' => $rule->subject_contains,
                'subject_regex' => $rule->subject_regex,
                'to_contains' => $rule->to_contains,
                'body_contains' => $rule->body_contains,
                'require_attachment' => $rule->require_attachment,
                'amount_min' => $rule->amount_min,
                'amount_max' => $rule->amount_max,
            ],
            'actions' => [
                'then_bank_account_id' => $rule->then_bank_account_id,
                'then_category' => $rule->then_category,
                'then_tag' => $rule->then_tag,
                'then_target_folder' => $rule->then_target_folder,
                'then_auto_approve' => $rule->then_auto_approve,
                'then_reject' => $rule->then_reject,
                'stop_processing' => $rule->stop_processing,
            ],
        ])->toArray();

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function exportToCsv(Collection $rules): string
    {
        $csv = "Name,Priority,Active,Match Type,From Contains,Subject Contains,Body Contains,Category,Auto Approve,Auto Reject\n";

        foreach ($rules as $rule) {
            $csv .= sprintf(
                "\"%s\",%d,%s,%s,\"%s\",\"%s\",\"%s\",\"%s\",%s,%s\n",
                str_replace('"', '""', $rule->name),
                $rule->priority,
                $rule->is_active ? 'Yes' : 'No',
                $rule->match_type,
                str_replace('"', '""', $rule->from_contains ?? ''),
                str_replace('"', '""', $rule->subject_contains ?? ''),
                str_replace('"', '""', $rule->body_contains ?? ''),
                $rule->then_category ?? '',
                $rule->then_auto_approve ? 'Yes' : 'No',
                $rule->then_reject ? 'Yes' : 'No'
            );
        }

        return $csv;
    }
}
