<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * User dashboard - main landing after login.
     */
    public function index()
    {
        // TODO: pull real data from DB — tasks, progress, reminders etc.
        $data = [
            'tasks_today'    => 5,
            'tasks_done'     => 2,
            'focus_minutes'  => 120,
            'streak_days'    => 7,
            // dummy task list for the board preview
            'recent_tasks'   => [
                ['id' => 1, 'title' => 'Finalize Q2 Report',      'status' => 'in_progress', 'priority' => 'high',   'due' => '2024-05-25'],
                ['id' => 2, 'title' => 'Team standup meeting',    'status' => 'done',        'priority' => 'medium', 'due' => '2024-05-20'],
                ['id' => 3, 'title' => 'Update onboarding docs',  'status' => 'todo',        'priority' => 'low',    'due' => '2024-05-30'],
                ['id' => 4, 'title' => 'Review design mockups',   'status' => 'done',        'priority' => 'medium', 'due' => '2024-05-18'],
                ['id' => 5, 'title' => 'Read 2 books this month', 'status' => 'in_progress', 'priority' => 'low',    'due' => '2024-05-31'],
            ],
        ];

        return view('dashboard.index', $data);
    }
}
