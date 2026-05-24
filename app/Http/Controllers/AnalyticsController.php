<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Show the analytics/reports page.
     */
    public function index()
    {
        // TODO: aggregate task stats, focus time, completion rates per week/month
        return view('analytics.index');
    }
}
