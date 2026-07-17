<?php

namespace App\Events;

use App\Models\PendingEmail;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailProcessedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PendingEmail $email,
        public string $action, // 'approved', 'rejected', 'failed'
        public ?User $operator = null,
    ) {
    }
}
