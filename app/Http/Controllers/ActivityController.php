<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activity::where('program_id')
            ->latest()
            ->paginate(6);

        return view('activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Program $program)
    {
        return view('activities.create', compact('program'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Program $program)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        Activity::create([
            'program_id' => $program->id,
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        notyf('Aktivitas berhasil dibuat!');
        return redirect()->route('programs.show', $program->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        if ($activity->program->user_id !== Auth::id()) {
            abort(403);
        }

        $activity->load('galleries');
        return view('activities.edit', compact('activity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        if ($activity->program->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $activity->update([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        notyf('Kegiatan berhasil diupdate!');
        return redirect()->route('programs.show', $activity->program);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        if ($activity->program->user_id !== Auth::id()) {
            abort(403);
        }

        $programId = $activity->program_id;
        $activity->delete();
        
        notyf('Kegiatan berhasil dihapus!');
        return redirect()->route('programs.show', $programId);
    }
}
