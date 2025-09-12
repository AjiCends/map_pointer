<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $uploadedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($request->file('photos') as $index => $photo) {
            try {
                $path = $photo->store('gallery', 'public');

                Gallery::create([
                    'activity_id' => $activity->id,
                    'image_url' => $path
                ]);

                $uploadedCount++;

            } catch (\Throwable $e) {
                $skippedCount++;
                $errors[] = "Foto ke-" . ($index + 1) . " (" . $photo->getClientOriginalName() . "): " . $e->getMessage();

                Log::error('Gallery upload error', [
                    'activity_id' => $activity->id,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'error' => $e->getMessage()
                ]);

                continue;
            }
        }

        if ($uploadedCount > 0) {
            $message = $uploadedCount . ' foto berhasil diupload!';
            if ($skippedCount > 0) {
                $message .= ' (' . $skippedCount . ' foto dilewati karena error)';
            }
            notyf()->success($message);
            return redirect()->route('gallery.index', $activity);
        } else {
            $errorMessage = 'Tidak ada foto yang berhasil diupload.';
            if (!empty($errors)) {
                $errorMessage .= ' Error: ' . implode(', ', array_slice($errors, 0, 2));
                if (count($errors) > 2) {
                    $errorMessage .= ' dan ' . (count($errors) - 2) . ' error lainnya.';
                }
            }
            notyf()->error($errorMessage);
            return redirect()->route('gallery.create', $activity);
        }
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

        notyf('Foto berhasil dihapus!');
        return redirect()->route('gallery.index', $activity);
    }

    /**
     * Download a photo from gallery.
     */
    public function download(Activity $activity, Gallery $gallery)
    {
        if ($activity->program->user_id !== Auth::id() || $gallery->activity_id !== $activity->id) {
            abort(403);
        }

        $path = $gallery->image_url;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $fileName = 'kegiatan-' . Str::slug($activity->name) . '-' . now()->format('YmdHis') . '.' . $extension;
        
        $fullPath = storage_path('app/public/' . $path);

        return response()->download($fullPath, $fileName);
    }
}
