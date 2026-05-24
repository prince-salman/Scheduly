<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Show the calendar view with tasks/events.
     */
    public function index()
    {
        // TODO: fetch events/tasks for the current month and pass to view
        return view('calendar.index');
    }
}
