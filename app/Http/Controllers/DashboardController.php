<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $currentUser = Auth::user();

        $programs = Program::with(['activities.galleries'])
            ->where('user_id', $userId)
            ->where('is_pin', 1)
            ->get();

        $pined_program = Program::with(['activities.galleries'])
            ->where('user_id', $userId)
            ->where('is_pin', 1)
            ->first();

        // $allPrograms = Program::with(['activities.galleries'])
        // ->where('is_pin', 1)
        // ->get();

        if (config('app.debug')) {
            Log::info('Dashboard Debug Info', [
                'current_user_id' => $userId,
                'current_user_name' => $currentUser->name ?? 'Unknown',
                'user_programs_count' => $programs->count(),
                // 'all_programs_count' => $allPrograms->count(),
                'user_program_names' => $programs->pluck('name')->toArray(),
                // 'all_program_users' => $allPrograms->pluck('user_id', 'name')->toArray()
            ]);
        }

        return view('dashboard', compact('programs', 'pined_program'));
    }
}
