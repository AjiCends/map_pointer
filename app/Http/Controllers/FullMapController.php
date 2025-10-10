<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class FullMapController extends Controller
{
    public function index(Request $request, $id)
    {
        $program = Program::with(['activities.galleries'])
            ->where('is_pin', 1)
            ->where('id', $id)
            ->first();

        $coordinates = $program
            ? $program->activities->map(fn($a) => [
                'id' => $a->id,
                'program_id' => $program->id,
                'program_name' => $program->name,
                'lat' => $a->latitude,
                'lng' => $a->longitude,
                'name' => $a->name,
                'desc' => $a->description ?? 'Tidak ada deskripsi',
            ])
            : collect(); // fallback kalau tidak ada program

        return view('full_map.index', compact('coordinates', 'program'));
    }
}
