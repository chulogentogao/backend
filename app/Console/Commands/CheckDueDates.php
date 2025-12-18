<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-due-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for due dates and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for due dates...');
        
        // Get transactions that are due today
        $dueToday = Transaction::with(['user', 'item'])
            ->where('status', 'borrowed')
            ->whereDate('due_date', Carbon::today())
            ->get();
            
        foreach ($dueToday as $transaction) {
            // Create notification for due today
            Notification::create([
                'user_id' => $transaction->user_id,
                'message' => "Your borrowed item '{$transaction->item->name}' is due today. Please return it to avoid penalties.",
                'status' => 'unread'
            ]);
            
            $this->info("Notification sent to user {$transaction->user->name} for item {$transaction->item->name} due today.");
        }
        
        // Get transactions that are due tomorrow
        $dueTomorrow = Transaction::with(['user', 'item'])
            ->where('status', 'borrowed')
            ->whereDate('due_date', Carbon::tomorrow())
            ->get();
            
        foreach ($dueTomorrow as $transaction) {
            // Create notification for due tomorrow
            Notification::create([
                'user_id' => $transaction->user_id,
                'message' => "Your borrowed item '{$transaction->item->name}' is due tomorrow. Please return it on time.",
                'status' => 'unread'
            ]);
            
            $this->info("Notification sent to user {$transaction->user->name} for item {$transaction->item->name} due tomorrow.");
        }
        
        // Get overdue transactions
        $overdue = Transaction::with(['user', 'item'])
            ->where('status', 'borrowed')
            ->whereDate('due_date', '<', Carbon::today())
            ->get();
            
        foreach ($overdue as $transaction) {
            // Create notification for overdue items
            Notification::create([
                'user_id' => $transaction->user_id,
                'message' => "Your borrowed item '{$transaction->item->name}' is overdue. Please return it immediately to avoid further penalties.",
                'status' => 'unread'
            ]);
            
            $this->info("Notification sent to user {$transaction->user->name} for overdue item {$transaction->item->name}.");
        }
        
        $this->info('Due date check completed.');
    }
}
