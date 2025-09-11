<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
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
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        if ($activity->program->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'photos' => 'required|array|max:10',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480'
        ]);

        foreach ($request->file('photos') as $index => $photo) {
            try {
                if ($index % 3 == 0) {
                    gc_collect_cycles();
                }

                $targetSize = 3 * 1024 * 1024;
                $originalSize = $photo->getSize();

                if ($originalSize <= $targetSize) {
                    $path = $photo->store('gallery', 'public');
                } else {
                    $image = Image::read($photo);

                    $maxWidth = 1920;
                    $maxHeight = 1080;

                    if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                        $image->scale(width: $maxWidth, height: $maxHeight);
                    }

                    $quality = 80;
                    $compressedData = null;
                    $attempts = 0;
                    $maxAttempts = 5;

                    do {
                        $compressedData = $image->toJpeg($quality);
                        $compressedSize = strlen($compressedData);

                        if ($compressedSize > $targetSize && $attempts < $maxAttempts) {
                            $quality -= 15;
                            $attempts++;
                        } else {
                            break;
                        }
                    } while ($quality > 30);

                    $filename = 'gallery/' . uniqid() . '.jpg';
                    Storage::disk('public')->put($filename, $compressedData);
                    $path = $filename;

                    unset($image);
                    unset($compressedData);
                }

                Gallery::create([
                    'activity_id' => $activity->id,
                    'image_url' => $path
                ]);
            } catch (\Throwable $e) {
                try {
                    $path = $photo->store('gallery', 'public');

                    Gallery::create([
                        'activity_id' => $activity->id,
                        'image_url' => $path
                    ]);
                } catch (\Exception $fallbackError) {
                    continue;
                }
            }
        }

        notyf('Foto berhasil diupload!');
        return redirect()->route('gallery.index', $activity);
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

        // Nama file custom, misalnya: kegiatan-nama_aktivitas-timestamp.jpg
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $fileName = 'kegiatan-' . Str::slug($activity->name) . '-' . now()->format('YmdHis') . '.' . $extension;

        return Storage::disk('public')->download($path, $fileName);
    }
}
