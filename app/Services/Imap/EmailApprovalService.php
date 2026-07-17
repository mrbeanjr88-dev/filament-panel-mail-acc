<?php

namespace App\Services\Imap;

use App\Events\EmailProcessedEvent;
use App\Models\PendingEmail;
use Illuminate\Support\Facades\Log;
use Throwable;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Message;

/**
 * Acțiunile de „vamă" asupra unui mail în carantină + actualizarea contului bancar.
 */
class EmailApprovalService
{
    public function __construct(private MimeBuilder $mimeBuilder)
    {
    }

    /**
     * Aprobă: mută mailul în Inbox (sau folderul țintă). Dacă a fost modificat (atașamente),
     * reconstruiește MIME-ul și-l face APPEND, apoi șterge originalul din Hold.
     */
    public function approve(PendingEmail $pending, ?string $by = null): void
    {
        $account = $pending->emailAccount;
        $target  = $pending->resolvedTargetFolder();

        try {
            $client = $account->makeClient();

            if ($pending->modified || $this->hasAttachmentChanges($pending)) {
                $client->getFolder($target)->appendMessage(
                    $this->mimeBuilder->build($pending),
                    null,
                    $pending->date?->toDateTimeString(),
                );
                $this->findInHold($client, $pending)?->delete(true);
            } else {
                $this->findInHold($client, $pending)?->move($target, true);
            }

            $pending->forceFill([
                'status' => 'processed', 'approved_at' => now(), 'approved_by' => $by, 'last_error' => null,
            ])->save();

            $client->disconnect();

            // Log the approval
            Log::info('email_approved', [
                'pending_email_id' => $pending->id,
                'approved_by' => $by,
                'subject' => $pending->subject,
                'from' => $pending->from_email,
                'target_folder' => $target,
            ]);

            // Dispatch event for webhooks/notifications
            $user = $by ? \App\Models\User::where('email', $by)->first() : null;
            event(new EmailProcessedEvent($pending, 'approved', $user));
        } catch (Throwable $e) {
            $pending->forceFill(['status' => 'failed', 'last_error' => $e->getMessage()])->save();

            Log::error('email_approval_failed', [
                'pending_email_id' => $pending->id,
                'error' => $e->getMessage(),
                'subject' => $pending->subject,
            ]);

            event(new EmailProcessedEvent($pending, 'failed', null));
            throw $e;
        }
    }

    public function reject(PendingEmail $pending, ?string $by = null): void
    {
        $account = $pending->emailAccount;

        try {
            $client = $account->makeClient();
            $this->findInHold($client, $pending)?->move($account->effectiveRejectedFolder(), true);

            $pending->forceFill([
                'status' => 'rejected', 'rejected_at' => now(), 'approved_by' => $by,
            ])->save();

            $client->disconnect();

            // Log the rejection
            Log::info('email_rejected', [
                'pending_email_id' => $pending->id,
                'rejected_by' => $by,
                'subject' => $pending->subject,
                'from' => $pending->from_email,
                'reason' => $account->effectiveRejectedFolder(),
            ]);

            // Dispatch event for webhooks/notifications
            $user = $by ? \App\Models\User::where('email', $by)->first() : null;
            event(new EmailProcessedEvent($pending, 'rejected', $user));
        } catch (Throwable $e) {
            $pending->forceFill(['status' => 'failed', 'last_error' => $e->getMessage()])->save();

            Log::error('email_rejection_failed', [
                'pending_email_id' => $pending->id,
                'error' => $e->getMessage(),
                'subject' => $pending->subject,
            ]);

            event(new EmailProcessedEvent($pending, 'failed', null));
            throw $e;
        }
    }

    /**
     * Reîncearcă mutarea unui mail din Inbox în Hold după un eșec anterior.
     * Caută mesajul în INBOX după Message-ID și îl mută în Hold.
     */
    public function retryHold(PendingEmail $pending): void
    {
        $account = $pending->emailAccount;

        $client = $account->makeClient();

        try {
            // Caută în INBOX după Message-ID
            $inbox = $client->getFolder($account->inbox_folder);
            $message = null;

            if ($pending->message_id) {
                $message = $inbox->query()
                    ->whereHeader('Message-ID', trim($pending->message_id, '<>'))
                    ->limit(1)
                    ->get()
                    ->first();
            }

            if (! $message) {
                throw new \RuntimeException('Message not found in Inbox. It may have been moved or deleted manually.');
            }

            $moved = $message->move($account->hold_folder, true);

            $pending->forceFill([
                'status'     => 'pending',
                'last_error' => null,
                'hold_uid'   => $moved instanceof \Webklex\PHPIMAP\Message ? $moved->getUid() : null,
            ])->save();

            $client->disconnect();
        } catch (\Throwable $e) {
            $client->disconnect();
            $pending->forceFill([
                'last_error' => 'Retry Hold failed: ' . $e->getMessage(),
            ])->save();
            throw $e;
        }
    }

    public function moveTo(PendingEmail $pending, string $folder): void
    {
        $client = $pending->emailAccount->makeClient();
        $this->findInHold($client, $pending)?->move($folder, true);
        $pending->forceFill(['status' => 'processed', 'approved_at' => now()])->save();
        $client->disconnect();
    }

    // ---- intern -----------------------------------------------------------------

    private function findInHold(Client $client, PendingEmail $pending): ?Message
    {
        $hold = $client->getFolder($pending->emailAccount->hold_folder);
        if (! $hold) {
            return null;
        }

        if ($pending->hold_uid) {
            try {
                return $hold->query()->getMessageByUid($pending->hold_uid);
            } catch (Throwable) {
                // fallback la Message-ID
            }
        }

        if ($pending->message_id) {
            return $hold->query()
                ->whereHeader('Message-ID', trim($pending->message_id, '<>'))
                ->limit(1)->get()->first();
        }

        return null;
    }

    private function hasAttachmentChanges(PendingEmail $pending): bool
    {
        return $pending->attachments()
            ->where(fn ($q) => $q->where('is_removed', true)->orWhere('is_replaced', true))
            ->exists();
    }
}
