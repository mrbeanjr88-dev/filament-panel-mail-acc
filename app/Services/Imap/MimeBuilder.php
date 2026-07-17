<?php

namespace App\Services\Imap;

use App\Models\PendingEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Reconstruiește un mesaj MIME brut din PendingEmail + atașamentele CURENTE (după ce
 * operatorul a șters/înlocuit fișiere). Folosit la „aprobare cu modificări": mesajul
 * original e imutabil pe server → construim unul nou și-l facem APPEND.
 */
class MimeBuilder
{
    public function build(PendingEmail $pending): string
    {
        $email = new Email();
        $email->from($this->address($pending->from_email, $pending->from_name));

        foreach ($pending->to ?? [] as $to) {
            if (! empty($to['email'])) {
                $email->addTo($this->address($to['email'], $to['name'] ?? null));
            }
        }
        foreach ($pending->cc ?? [] as $cc) {
            if (! empty($cc['email'])) {
                $email->addCc($this->address($cc['email'], $cc['name'] ?? null));
            }
        }

        $email->subject((string) $pending->subject);
        if ($pending->date) {
            $email->date($pending->date);
        }
        if ($pending->body_html) {
            $email->html($pending->body_html);
        }
        if ($pending->body_text) {
            $email->text($pending->body_text);
        }

        foreach ($pending->activeAttachments as $attachment) {
            if ($attachment->is_inline && $attachment->content_id) {
                $email->embed(
                    $attachment->contents(),
                    trim($attachment->content_id, '<>'),
                    $attachment->mime_type ?: 'application/octet-stream',
                );
            } else {
                $email->attach(
                    $attachment->contents(),
                    $attachment->filename,
                    $attachment->mime_type ?: 'application/octet-stream',
                );
            }
        }

        return $email->toString();
    }

    private function address(?string $email, ?string $name): Address
    {
        return new Address($email ?: 'unknown@localhost', $name ?: '');
    }
}
