<?php
namespace App\Console\Commands;

use App\Models\EmailAccount;
use App\Services\Imap\ImapClientFactory;
use Illuminate\Console\Command;

class TestImap extends Command
{
    protected $signature = 'app:test-imap';
    protected $description = 'Test IMAP connections for all email accounts';

    public function handle(): int
    {
        foreach (EmailAccount::all() as $account) {
            $this->info("Testing: {$account->email}");
            try {
                $client = app(ImapClientFactory::class)->for($account);
                $folders = $client->getFolders();
                $this->info("  Connected! Folders: " . count($folders));
                foreach ($folders as $folder) {
                    $this->line("    - {$folder->full_name} ({$folder->getMessageCount()})");
                }
                $client->disconnect();
            } catch (\Exception $e) {
                $this->error("  ERROR: " . $e->getMessage());
            }
        }
        return 0;
    }
}
