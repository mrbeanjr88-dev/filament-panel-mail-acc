<?php

return [
    // Disk implicit pentru atașamente (override per setări în AppSetting).
    // Folosește un disk PRIVAT — sunt date sensibile.
    'attachments_disk' => env('EMAIL_CUSTOMS_DISK', 'local'),

    // Timeout conexiune IMAP (secunde).
    'connection_timeout' => env('EMAIL_CUSTOMS_TIMEOUT', 30),

    /*
    | OAuth2 (Gmail / Outlook). Basic Auth IMAP e tot mai restricționat. Pentru producție
    | setează authentication='oauth' pe cont și pasează un access token (XOAUTH2) ca parolă.
    */
    'oauth' => [
        'google' => [
            'client_id'     => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect'      => env('GOOGLE_REDIRECT_URI'),
        ],
        'microsoft' => [
            'client_id'     => env('MS_CLIENT_ID'),
            'client_secret' => env('MS_CLIENT_SECRET'),
            'redirect'      => env('MS_REDIRECT_URI'),
        ],
    ],

    // Webhook URL pentru notificări de email procesate
    'webhook_url' => env('EMAIL_CUSTOMS_WEBHOOK_URL'),

    // Email archival settings
    'archive' => [
        'enabled' => env('EMAIL_CUSTOMS_ARCHIVE_ENABLED', true),
        'archive_after_days' => env('EMAIL_CUSTOMS_ARCHIVE_AFTER_DAYS', 90),
        'permanent_delete_after_days' => env('EMAIL_CUSTOMS_PERMANENT_DELETE_AFTER_DAYS', 365),
    ],
];
