<?php

namespace App\Services\Imap;

use App\Enums\PendingEmailStatus;
use App\Models\AppSetting;
use App\Models\EmailAccount;
use App\Models\PendingEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Message;

/**
 * Flux mandator de carantină:
 *   INBOX → Hold (carantină) → verificare umană → Inbox (sau Rejected)
 *
 * Reguli fixe (non-negociabile):
 *  - ORICE mail din Inbox este mutat în Hold ÎNAINTE ca operatorul să-l vadă.
 *  - Auto-aprobarea prin reguli este PERMANENT DEZACTIVATĂ.
 *  - Dacă mutarea în Hold eșuează, mailul este salvat ca status=failed (vizibil în panou).
 *  - Operatorul poate reîncerca mutarea în Hold din panou.
 */
class EmailSyncService
{
    public function __construct(
        private RuleEngine $rules,
        private BankDataExtractor $extractor,
        private EmailApprovalService $approvals,
    ) {
    }

    public function sync(EmailAccount $account): int
    {
        $settings = AppSetting::current();
        $captured = 0;

        try {
            $client = $account->makeClient();
            $this->ensureFolder($client, $account->hold_folder);

            $inbox = $client->getFolder($account->inbox_folder);

            // Folosim 'all' pentru a nu rata mailuri deja citite în alt client.
            // Verificarea idempotentă pe message_id previne duplicatele.
            $query = $inbox->query()->setFetchOrderDesc()->limit($account->fetch_limit ?: 50);
            $query = $settings->capture_mode === 'unseen' ? $query->unseen() : $query->all();

            foreach ($query->get() as $message) {
                try {
                    if ($this->capture($account, $message, $settings)) {
                        $captured++;
                    }
                } catch (Throwable $e) {
                    Log::error('Email capture failed', [
                        'account' => $account->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }

            $account->forceFill(['last_synced_at' => now(), 'last_error' => null])->save();
            $client->disconnect();
        } catch (Throwable $e) {
            $account->forceFill(['last_error' => $e->getMessage()])->save();
            Log::error('Email sync failed', ['account' => $account->id, 'error' => $e->getMessage()]);
            throw $e;
        }

        return $captured;
    }

    private function capture(EmailAccount $account, Message $message, AppSetting $settings): bool
    {
        $messageId = $this->messageId($message);

        // Idempotent: message_id deja în DB (orice status, inclusiv failed).
        if ($messageId && PendingEmail::where('email_account_id', $account->id)
            ->where('message_id', $messageId)->exists()) {
            return false;
        }

        // ── Step 1: Salvează mailul în DB (tranzacție doar pentru DB) ───────────────
        $pending = DB::transaction(function () use ($account, $message, $messageId, $settings) {
            $from = $message->getFrom()[0] ?? null;

            $pending = PendingEmail::create([
                'email_account_id' => $account->id,
                'message_id'       => $messageId,
                'subject'          => (string) $message->getSubject(),
                'from_name'        => $from->personal ?? null,
                'from_email'       => $from->mail ?? null,
                'to'               => $this->addresses($message->getTo()),
                'cc'               => $this->addresses($message->getCc()),
                'date'             => optional($message->getDate())->toDate(),
                'body_html'        => $message->getHTMLBody(),
                'body_text'        => $message->getTextBody(),
                'raw_headers'      => (string) $message->getHeader(),
                'size'             => (int) $message->getSize(),
                'has_attachments'  => $message->hasAttachments(),
                'status'           => 'pending',
            ]);

            $this->storeAttachments($pending, $message, $settings);

            // Extrage date factură / tranzacție din corpul mailului.
            if ($settings->extract_bank_data) {
                $data = $this->extractor->extract($pending->body_text ?: (string) $pending->body_html);
                $pending->fill([
                    'extracted_amount'    => $data['amount'],
                    'extracted_currency'  => $data['currency'] ?? $settings->default_currency,
                    'extracted_direction' => $data['direction'],
                    'invoice_number'      => $data['invoice_number'] ?? null,
                    'invoice_date'        => $data['invoice_date'] ?? null,
                    'due_date'            => $data['due_date'] ?? null,
                    'invoice_issuer'      => $data['invoice_issuer'] ?? null,
                    'vat_amount'          => $data['vat_amount'] ?? null,
                ]);
            }

            // Aplică metadate din reguli (cont, categorie, tag, folder, matched_rule_id).
            // AUTO-APROBARE din reguli este DEZACTIVATĂ PERMANENT — niciun mail nu e aprobat automat.
            if ($settings->auto_apply_rules) {
                $this->rules->apply($pending);
            }

            $pending->save();

            return $pending;
        });

        // ── Step 2: Mută în Hold (OBLIGATORIU, în afara tranzacției DB) ─────────────
        // IMAP nu e tranzacțional cu DB: dacă facem mutarea în interiorul tranzacției
        // și DB commit eșuează, mailul ajunge în Hold fără înregistrare în DB.
        // Dacă mutarea eșuează, înregistrăm eroarea — operatorul poate reîncerca.
        $this->moveToHold($account, $message, $pending);

        // ── Step 3: Auto-respinge doar dacă regula prevede explicit (spam etc.) ─────
        // Auto-aprobare este NICIODATĂ executată — toate mailurile cer verificare umană.
        $this->postRuleActions($pending);

        return true;
    }

    /**
     * Mută mesajul din Inbox în folderul Hold al contului.
     * Dacă eșuează, marchează PendingEmail ca failed (vizibil în panou pentru retry).
     */
    private function moveToHold(EmailAccount $account, Message $message, PendingEmail $pending): void
    {
        try {
            $moved = $message->move($account->hold_folder, true);
            if ($moved instanceof Message) {
                $pending->update(['hold_uid' => $moved->getUid()]);
            }
        } catch (Throwable $e) {
            Log::error('Hold move failed — email saved as failed', [
                'pending_email_id' => $pending->id,
                'account_id'       => $account->id,
                'subject'          => $pending->subject,
                'error'            => $e->getMessage(),
            ]);
            $pending->forceFill([
                'status'     => PendingEmailStatus::Failed,
                'last_error' => 'Move to Hold failed: ' . $e->getMessage(),
            ])->save();
        }
    }

    /**
     * Auto-respinge mailul dacă regula o cere (ex: spam).
     * Auto-aprobarea este PERMANENT DEZACTIVATĂ — toate mailurile cer intervenție umană.
     */
    private function postRuleActions(PendingEmail $pending): void
    {
        $rule = $pending->matchedRule;

        // Nu executa acțiuni pe mailuri care nu au ajuns în Hold (status=failed).
        if (! $rule || $pending->status === PendingEmailStatus::Failed) {
            return;
        }

        if ($rule->then_reject) {
            try {
                $this->approvals->reject($pending, 'rule:' . $rule->name);
            } catch (Throwable $e) {
                Log::warning('Auto-reject failed', [
                    'email' => $pending->id,
                    'rule'  => $rule->name,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        // then_auto_approve este ignorat complet — eliminat din UI, dezactivat în logică.
    }

    private function storeAttachments(PendingEmail $pending, Message $message, AppSetting $settings): void
    {
        $disk = $settings->attachments_disk ?: 'local';
        $hasPdf = false;

        /** @var Attachment $attachment */
        foreach ($message->getAttachments() as $attachment) {
            $name = $attachment->getName() ?: ('attachment-' . Str::random(6));
            $path = sprintf('email-customs/%d/%s-%s', $pending->id, Str::random(8), $name);

            Storage::disk($disk)->put($path, $attachment->getContent());

            $mimeType = $attachment->getMimeType();
            $isPdf = $mimeType === 'application/pdf' || str_ends_with(strtolower($name), '.pdf');

            $pending->attachments()->create([
                'filename'   => $name,
                'mime_type'  => $mimeType,
                'size'       => (int) $attachment->getSize(),
                'disk'       => $disk,
                'path'       => $path,
                'content_id' => $attachment->getId(),
                'is_inline'  => $attachment->getDisposition() === 'inline',
            ]);

            if ($isPdf) {
                $hasPdf = true;
            }
        }

        // Interceptare PDF: marchează emailul și aplică template-uri auto
        if ($hasPdf && $settings->intercept_pdf_attachments) {
            $pending->update([
                'pdf_intercepted' => true,
            ]);

            if ($settings->auto_apply_pdf_templates) {
                $this->applyAutoPdfTemplates($pending, $disk);
            }
        }
    }

    /**
     * Aplică automat template-urile PDF care au auto_apply activ și se potrivesc cu emailul.
     */
    private function applyAutoPdfTemplates(PendingEmail $pending, string $disk): void
    {
        $templates = \App\Models\PdfTemplate::query()
            ->where('is_active', true)
            ->where('auto_apply', true)
            ->orderBy('priority')
            ->get();

        $pdfService = app(\App\Services\PdfModifierService::class);

        foreach ($templates as $template) {
            // Verifică dacă se potrivește cu emailul
            if ($template->email_account_id && $template->email_account_id !== $pending->email_account_id) {
                continue;
            }

            if (! $template->matches($pending)) {
                continue;
            }

            // Aplică template-ul pe fiecare PDF din atașamente
            $pdfAttachments = $pending->pdfAttachments()->get();

            foreach ($pdfAttachments as $attachment) {
                try {
                    $result = $pdfService->apply(
                        $attachment->path,
                        $attachment->disk,
                        [$template->toOperationParams()],
                        'auto-' . $template->id . '-' . $attachment->filename
                    );

                    $pending->attachments()->create([
                        'filename'   => 'auto-' . $attachment->filename,
                        'mime_type'  => 'application/pdf',
                        'size'       => $result['size'],
                        'disk'       => $result['disk'],
                        'path'       => $result['path'],
                        'is_inline'  => false,
                    ]);

                    $pending->update([
                        'modified'                => true,
                        'pdf_modified'            => true,
                        'pdf_pages_modified'      => $result['pages'],
                        'pdf_modification_summary' => trim(($pending->pdf_modification_summary ?? '') . "\nAuto: " . $template->name),
                    ]);

                    \App\Models\PdfModificationLog::create([
                        'pending_email_id'        => $pending->id,
                        'pending_email_attachment_id' => $attachment->id,
                        'pdf_template_id'         => $template->id,
                        'operation'               => $template->type,
                        'params'                  => $template->config,
                        'result'                  => $result,
                        'status'                  => 'completed',
                    ]);
                } catch (Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Auto PDF template failed', [
                        'pending_email_id' => $pending->id,
                        'template_id'      => $template->id,
                        'error'            => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private function ensureFolder($client, string $folder): void
    {
        try {
            if (! $client->getFolder($folder)) {
                $client->createFolder($folder, false);
            }
        } catch (Throwable) {
            try {
                $client->createFolder($folder, false);
            } catch (Throwable) {
                // Serverul poate refuza crearea; sync-ul continuă.
            }
        }
    }

    private function messageId(Message $message): ?string
    {
        $id = $message->getMessageId();

        return $id ? trim((string) $id) : null;
    }

    private function addresses($collection): array
    {
        $out = [];
        foreach ($collection ?? [] as $address) {
            $out[] = ['name' => $address->personal ?? null, 'email' => $address->mail ?? null];
        }

        return $out;
    }
}
