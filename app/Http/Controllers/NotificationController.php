<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Show the notifications list.
     */
    public function index()
    {
        // TODO: load notifications for auth user, paginate, mark as read
        return view('notifications.index');
    }
}
