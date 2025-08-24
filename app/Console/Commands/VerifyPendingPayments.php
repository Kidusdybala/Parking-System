<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChapaTransaction;
use App\Services\ChapaService;
use Illuminate\Support\Facades\Log;

class VerifyPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapa:verify-pending {--all : Verify all pending payments} {--recent : Verify payments from last 24 hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify pending Chapa payments and update balances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting Chapa payment verification...');
        
        $chapaService = new ChapaService();
        
        // Get pending transactions
        $query = ChapaTransaction::where('status', ChapaTransaction::STATUS_PENDING);
        
        if ($this->option('recent')) {
            $query->where('created_at', '>=', now()->subDay());
            $this->info('📅 Checking payments from last 24 hours...');
        } elseif (!$this->option('all')) {
            // Default: check payments from last 2 hours
            $query->where('created_at', '>=', now()->subHours(2));
            $this->info('📅 Checking payments from last 2 hours...');
        } else {
            $this->info('📅 Checking ALL pending payments...');
        }
        
        $pendingTransactions = $query->get();
        
        if ($pendingTransactions->isEmpty()) {
            $this->info('✅ No pending transactions found.');
            return;
        }
        
        $this->info("📋 Found {$pendingTransactions->count()} pending transactions");
        
        $verified = 0;
        $failed = 0;
        $errors = 0;
        
        foreach ($pendingTransactions as $transaction) {
            $this->line("🔍 Verifying: {$transaction->tx_ref}");
            
            try {
                $result = $chapaService->verifyPayment($transaction->tx_ref);
                
                if ($result['status'] === 'success') {
                    $this->info("   ✅ Verified and processed");
                    $verified++;
                } else {
                    $this->warn("   ⚠️  Still pending or failed: " . $result['message']);
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
                $errors++;
            }
            
            // Small delay to avoid rate limiting
            usleep(500000); // 0.5 seconds
        }
        
        $this->newLine();
        $this->info("📊 Verification Summary:");
        $this->info("   ✅ Verified: {$verified}");
        $this->info("   ⚠️  Failed/Pending: {$failed}");
        $this->info("   ❌ Errors: {$errors}");
        
        if ($verified > 0) {
            $this->info("🎉 {$verified} payments processed successfully!");
        }
        
        return 0;
    }
}