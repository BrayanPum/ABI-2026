<?php

namespace App\Events;

use App\Models\AcademicProcessWindow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicProcessWindowClosing
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public AcademicProcessWindow $window, public int $daysLeft)
    {
    }
}
