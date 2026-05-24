<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Admin overview page.
     */
    public function index()
    {
        // TODO: fetch real platform stats — total users, tasks completed this week,
        //       pending approvals count, reported issues, chart data
        return view('admin.dashboard');
    }
}
