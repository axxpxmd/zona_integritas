<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the CMS dashboard.
     */
    public function index()
    {
        return view('page.dashboard');
    }
}
