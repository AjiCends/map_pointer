<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Program::with('activities')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
            
        return view('programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('programs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        Program::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('programs.index')
            ->with('success', 'Program berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        // Pastikan user hanya bisa melihat program miliknya sendiri
        if ($program->user_id !== Auth::id()) {
            abort(403);
        }

        $program->load('activities.photos');
        
        return view('programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        // Pastikan user hanya bisa edit program miliknya sendiri
        if ($program->user_id !== Auth::id()) {
            abort(403);
        }

        return view('programs.edit', compact('program'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        if ($program->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $program->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('programs.index')
            ->with('success', 'Program berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        // Pastikan user hanya bisa delete program miliknya sendiri
        if ($program->user_id !== Auth::id()) {
            abort(403);
        }

        $program->delete();

        return redirect()->route('programs.index')
            ->with('success', 'Program berhasil dihapus!');
    }
}
