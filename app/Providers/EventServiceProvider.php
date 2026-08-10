<?php

namespace App\Providers;

use App\Events\ProjectIdeaEvaluated;
use App\Events\UserCreated;
use App\Events\AcademicProcessWindowOpened;
use App\Events\AcademicProcessWindowClosing;
use App\Listeners\SendNotificationListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ProjectIdeaEvaluated::class => [
            SendNotificationListener::class,
        ],
        UserCreated::class => [
            SendNotificationListener::class,
        ],
        AcademicProcessWindowOpened::class => [
            SendNotificationListener::class,
        ],
        AcademicProcessWindowClosing::class => [
            SendNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
