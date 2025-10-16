<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class FullMapController extends Controller
{
    public function index(Request $request, $id = null)
    {
        if ($id != null) {
            $program = Program::with(['activities' => function ($q) {
                $q->where('is_hide', 0)->with('galleries');
            }])->where('id', $id)->first();
        } else {
            $program = Program::with(['activities' => function ($q) {
                $q->where('is_hide', 0)->with('galleries');
            }])->where('is_pin', 1)->first();
        }

        $coordinates = $program
            ? $program->activities->map(fn($a) => [
                'id' => $a->id,
                'program_id' => $program->id,
                'program_name' => $program->name,
                'lat' => $a->latitude,
                'lng' => $a->longitude,
                'name' => $a->name,
                'desc' => $a->description ?? 'Tidak ada deskripsi',
                'order_num' => $a->order_num,
            ])
            : collect(); // fallback kalau tidak ada program

        return view('full_map.index', compact('coordinates', 'program'));
    }
}
