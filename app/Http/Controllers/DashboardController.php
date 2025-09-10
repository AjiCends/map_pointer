<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua activity beserta nama programnya
        $activities = Activity::with('program')->get();

        return view('dashboard', compact('activities'));
    }
}
