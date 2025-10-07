<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class FullMapController extends Controller
{
    public function index(Request $request)
    {
        // $userId = auth()->id();

        $programs = Program::with(['activities.galleries'])
            // ->where('user_id', $userId)
            ->where('is_pin', 1)
            ->get();

        $coordinates = $programs->flatMap(
            fn($program) => $program->activities->map(fn($a) => [
                'id' => $a->id,
                'program_id' => $program->id,
                'program_name' => $program->name,
                'lat' => $a->latitude,
                'lng' => $a->longitude,
                'name' => $a->name,
                'desc' => $a->description ?? 'Tidak ada deskripsi',
            ])
        );

        return view('full_map.index', compact('coordinates'));
    }


}
