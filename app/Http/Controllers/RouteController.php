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

        $activities = $program->activities()->get();
        
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

        $selectedActivities = Activity::whereIn('id', $request->selected_activities)
            ->where('program_id', $program->id)
            ->orderBy('created_at', 'asc')
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
            $origin = $selectedActivities->first();
            $destination = $selectedActivities->last();
            $waypoints = $selectedActivities->slice(1, -1);
            $googleMapsUrl = $this->buildGoogleMapsUrl($origin, $destination, $waypoints);
        }
        
        return response()->json([
            'success' => true,
            'google_maps_url' => $googleMapsUrl
        ]);
    }

    /**
     * Build Google Maps URL with waypoints
     */
    private function buildGoogleMapsUrl($origin, $destination, $waypoints)
    {
        $baseUrl = 'https://www.google.com/maps/dir/';
        
        $url = $baseUrl . $origin->latitude . ',' . $origin->longitude;
        
        foreach ($waypoints as $waypoint) {
            $url .= '/' . $waypoint->latitude . ',' . $waypoint->longitude;
        }
        
        $url .= '/' . $destination->latitude . ',' . $destination->longitude;
        
        $url .= '?entry=ttu&g_ep=EgoyMDI1MDkxMS4wIKXMDSoASAFQAw%3D%3D';
        
        return $url;
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