<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        \App\Events\DrinkCreated::class => [
            \App\Listeners\InvalidateCache::class,
        ],
        \App\Events\DrinkUpdated::class => [
            \App\Listeners\InvalidateCache::class,
        ],
        \App\Events\DrinkDeleted::class => [
            \App\Listeners\InvalidateCache::class,
        ],
        \App\Events\DrinkCategoryCreated::class => [
            \App\Listeners\InvalidateCache::class,
        ],
        \App\Events\DrinkCategoryUpdated::class => [
            \App\Listeners\InvalidateCache::class,
        ],
        \App\Events\DrinkCategoryDeleted::class => [
            \App\Listeners\InvalidateCache::class,
        ],
        \App\Events\DrinkUnitCreated::class => [
            \App\Listeners\InvalidateCache::class,
        ],
        \App\Events\DrinkUnitUpdated::class => [
            \App\Listeners\InvalidateCache::class,
        ],
        \App\Events\DrinkUnitDeleted::class => [
            \App\Listeners\InvalidateCache::class,
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
