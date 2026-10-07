<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('koordinator.dashboard');
    }
}
