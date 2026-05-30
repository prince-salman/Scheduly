<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\CalendarEvent;
use App\Policies\TaskPolicy;
use App\Policies\CalendarEventPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(CalendarEvent::class, CalendarEventPolicy::class);
    }
}