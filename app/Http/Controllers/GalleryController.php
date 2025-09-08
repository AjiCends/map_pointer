<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource for specific activity.
     */
    public function index(Activity $activity)
    {
        if ($activity->program->user_id !== Auth::id()) {
            abort(403);
        }

        $galleries = $activity->galleries()->paginate(12);
        
        return view('gallery.index', compact('activity', 'galleries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Activity $activity)
    {
        if ($activity->program->user_id !== Auth::id()) {
            abort(403);
        }

        return view('gallery.create', compact('activity'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Activity $activity)
    {
        if ($activity->program->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'photos' => 'required|array|max:10',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('gallery', 'public');
            
            Gallery::create([
                'activity_id' => $activity->id,
                'image_url' => $path
            ]);
        }

        return redirect()->route('gallery.index', $activity)
            ->with('success', 'Foto berhasil diupload!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity, Gallery $gallery)
    {
        if ($activity->program->user_id !== Auth::id() || $gallery->activity_id !== $activity->id) {
            abort(403);
        }

        if ($gallery->image_url) {
            Storage::disk('public')->delete($gallery->image_url);
        }

        $gallery->delete();

        return redirect()->route('gallery.index', $activity)
            ->with('success', 'Foto berhasil dihapus!');
    }
}
