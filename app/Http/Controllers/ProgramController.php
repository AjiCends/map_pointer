<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Flasher\Notyf\Prime\notyf;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Program::with([
            'activities.galleries' => function ($query) {
                $query->orderBy('created_at', 'asc')->limit(1);
            }
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(6);

        foreach ($programs as $program) {
            $firstPhoto = null;
            $earliestDate = null;

            foreach ($program->activities as $activity) {
                foreach ($activity->galleries as $gallery) {
                    if ($earliestDate === null || $gallery->created_at < $earliestDate) {
                        $earliestDate = $gallery->created_at;
                        $firstPhoto = $gallery;
                    }
                }
            }

            $program->first_photo = $firstPhoto;
        }

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

        notyf()->success('Program berhasil ditambahkan!');
        return redirect()->route('programs.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        if ($program->user_id !== Auth::id()) {
            notyf()
                ->addError('Anda tidak memiliki akses ke program ini.');
            return redirect()->route('programs.index');
        }

        $program->load('activities.galleries');

        return view('programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
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

        notyf()->success('Program berhasil diperbarui!');
        return redirect()->route('programs.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        if ($program->user_id !== Auth::id()) {
            abort(403);
        }

        $program->delete();
        notyf()->error('Program berhasil dihapus!');
        return redirect()->route('programs.index');
    }
}
