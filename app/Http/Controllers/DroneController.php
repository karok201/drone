<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\MapPoint;
use Illuminate\Http\JsonResponse;

class DroneController extends Controller
{
    public function getCurrentLocations(): JsonResponse
    {

        $result = [];

        $drones = MapPoint::query()->where('user_id', 3)->get();

        foreach ($drones as $drone) {
            $result[] = [
                'id' => $drone->id,
                'lgt' => $drone->last_longitude ?? $drone->longitude,
                'lat' => $drone->last_latitude ?? $drone->latitude
            ];
        }

        return response()->json($result);
    }
}
