<?php

namespace App\Console\Commands;

use App\Events\AcademicProcessWindowClosing;
use App\Events\AcademicProcessWindowOpened;
use App\Models\AcademicProcessWindow;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyProcessWindows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abi:notify-windows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for academic process windows opening or closing and dispatch notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $this->info("Checking windows at: " . $now->toDateTimeString());

        // Process Window Opening
        $this->checkOpeningWindows($now);

        // Process Window Closing (e.g., 7 days before)
        $this->checkClosingWindows($now);

        return self::SUCCESS;
    }

    protected function checkOpeningWindows(Carbon $now)
    {
        $windows = AcademicProcessWindow::query()
            ->where('process_key', AcademicProcessWindow::PROCESS_IDEA_PROPOSAL)
            ->where('is_enabled', true)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->whereNull('opened_notification_sent_at')
            ->get();

        foreach ($windows as $window) {
            $this->info("Dispatching opening notification for: " . $window->name);
            AcademicProcessWindowOpened::dispatch($window);
            
            $window->opened_notification_sent_at = $now;
            $window->save();
        }
    }

    protected function checkClosingWindows(Carbon $now)
    {
        $daysBefore = 7;
        $targetDate = $now->copy()->addDays($daysBefore);

        $windows = AcademicProcessWindow::query()
            ->where('process_key', AcademicProcessWindow::PROCESS_IDEA_PROPOSAL)
            ->where('is_enabled', true)
            ->where('end_at', '<=', $targetDate)
            ->where('end_at', '>', $now)
            ->whereNull('closing_notification_sent_at')
            ->get();

        foreach ($windows as $window) {
            $this->info("Dispatching closing notification for: " . $window->name);
            $daysLeft = (int) $now->diffInDays($window->end_at);
            AcademicProcessWindowClosing::dispatch($window, $daysLeft);
            
            $window->closing_notification_sent_at = $now;
            $window->save();
        }
    }
}
