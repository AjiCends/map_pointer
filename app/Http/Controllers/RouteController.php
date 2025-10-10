<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    /**
     * Show route planner for a program
     */
    public function index(Program $program)
    {
        if ($program->user_id !== Auth::id()) {
            abort(403);
        }

        $activities = $program->activities()->orderBy('order_num', 'asc')->get();

        return view('routes.index', compact('program', 'activities'));
    }

    /**
     * Generate Google Maps route URL and return as JSON for frontend
     */
    public function generateRoute(Request $request, Program $program)
    {
        if ($program->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'selected_activities' => 'required|array|min:1',
            'selected_activities.*' => 'exists:activities,id',
            'use_current_location' => 'sometimes|in:on,1,true',
            'current_lat' => 'required_if:use_current_location,on|nullable|numeric',
            'current_lng' => 'required_if:use_current_location,on|nullable|numeric'
        ]);

        // dd($request->selected_activities);

        $activitiesIds = array_map('intval', $request->selected_activities);
        $selectedActivities = Activity::whereIn('id', $activitiesIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $activitiesIds) . ')')
            ->get();

        $useCurrentLocation = $request->filled('use_current_location') && $request->use_current_location;

        $totalPoints = $selectedActivities->count();
        if ($useCurrentLocation) {
            $totalPoints += 1;
        }

        if ($totalPoints < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih minimal 2 titik untuk membuat rute!'
            ], 400);
        }

        if ($useCurrentLocation) {
            $googleMapsUrl = $this->buildGoogleMapsUrlWithCurrentLocation(
                $request->current_lat,
                $request->current_lng,
                $selectedActivities
            );
        } else {
            $googleMapsUrl = $this->buildGoogleMapsUrl($selectedActivities);
        }

        return response()->json([
            'success' => true,
            'google_maps_url' => $googleMapsUrl
        ]);
    }

    /**
     * Build Google Maps URL with waypoints
     */
    private function buildGoogleMapsUrl($selectedActivities)
    {
        $baseUrl = 'https://www.google.com/maps/dir';

        foreach ($selectedActivities as $waypoint) {
            $baseUrl .= '/' . $waypoint->latitude . ',' . $waypoint->longitude;
        }

        $baseUrl .= '?entry=ttu&g_ep=EgoyMDI1MDkxMS4wIKXMDSoASAFQAw%3D%3D';

        return $baseUrl;
    }

    /**
     * Build Google Maps URL with current location as starting point
     */
    private function buildGoogleMapsUrlWithCurrentLocation($currentLat, $currentLng, $activities)
    {
        $baseUrl = 'https://www.google.com/maps/dir/';

        $url = $baseUrl . $currentLat . ',' . $currentLng;

        foreach ($activities as $activity) {
            $url .= '/' . $activity->latitude . ',' . $activity->longitude;
        }

        $url .= '?entry=ttu&g_ep=EgoyMDI1MDkxMS4wIKXMDSoASAFQAw%3D%3D';

        return $url;
    }
}
