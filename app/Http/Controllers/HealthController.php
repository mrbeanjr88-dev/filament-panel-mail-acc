<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'imap_accounts' => $this->checkImapAccounts(),
            'queue' => $this->checkQueue(),
        ];

        $healthy = collect($checks)->every(fn ($c) => $c['ok'] ?? false);

        return response()->json([
            'status' => $healthy ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }

    private function checkDatabase(): array
    {
        try {
            \DB::connection()->getPdo();
            $pendingCount = \DB::table('pending_emails')
                ->where('status', 'pending')
                ->count();

            return [
                'ok' => true,
                'message' => 'Database connection OK',
                'pending_emails' => $pendingCount,
            ];
        } catch (QueryException $e) {
            return [
                'ok' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            \Cache::put('health_check', time(), 10);
            $value = \Cache::get('health_check');

            if ($value === null) {
                throw new \Exception('Cache read/write failed');
            }

            return [
                'ok' => true,
                'message' => 'Cache OK',
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => 'Cache check failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkImapAccounts(): array
    {
        try {
            $activeCount = EmailAccount::where('is_active', true)->count();
            $failedCount = EmailAccount::where('is_active', true)
                ->whereNotNull('last_error')
                ->count();

            return [
                'ok' => $failedCount === 0,
                'message' => "{$activeCount} active, {$failedCount} with errors",
                'active_accounts' => $activeCount,
                'accounts_with_errors' => $failedCount,
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => 'IMAP check failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkQueue(): array
    {
        try {
            $pendingJobs = \DB::table('jobs')->count();
            $failedJobs = \DB::table('failed_jobs')->count();

            $threshold = config('app.env') === 'production' ? 100 : 1000;

            return [
                'ok' => $pendingJobs < $threshold && $failedJobs < 10,
                'message' => "{$pendingJobs} pending, {$failedJobs} failed",
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => 'Queue check failed: ' . $e->getMessage(),
            ];
        }
    }
}
