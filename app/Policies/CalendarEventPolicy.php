<?php

namespace App\Policies;

use App\Models\CalendarEvent;
use App\Models\User;

class CalendarEventPolicy
{
    public function delete(User $user, CalendarEvent $event): bool
    {
        return $user->id === $event->user_id || $user->isAdmin();
    }
}