<?php

namespace App\Http\Controllers;

use App\Models\MapPoint;
use Illuminate\Http\Request;

class DroneController extends Controller
{
    public function getCurrentLocations(): \Illuminate\Http\JsonResponse
    {
        $drones = MapPoint::query()->get();

        $result = [];

        foreach ($drones as $drone) {
            $lastLongitude = $drone->last_longitude ?? null;
            $lastLatitude = $drone->last_latitude ?? null;

            if ($lastLatitude == $drone->latitude && $lastLongitude == $drone->longitude) {
                $lastLongitude = $lastLatitude = null;
            }

            $path = json_decode($drone->path, true);

            if ($lastLongitude == null && $lastLatitude == null) {
                $loc = reset($path);

                $result[] = [
                    'id' => $drone->id,
                    'lgt' => $loc[0],
                    'lat' => $loc[1]
                ];

                $drone->update([
                    MapPoint::FIELD_LAST_LONGITUDE => $loc[0],
                    MapPoint::FIELD_LAST_LATITUDE => $loc[1]
                ]);

                continue;
            }

            foreach ($path as $key => $item) {
                $item[0] = (string) $item[0];
                $item[1] = (string) $item[1];
//                dd($item[0] == $lastLongitude);
                if ($item[0] == $lastLongitude && $item[1] == $lastLatitude) {
                    $item = $path[$key + 1] ?? null;

                    if (!$item) {
                        $result[] = [
                            'id' => $drone->id,
                            'lgt' => (float) $drone->longitude,
                            'lat' => (float) $drone->latitude
                        ];

                        $drone->update([
                            MapPoint::FIELD_LAST_LONGITUDE => $drone->longitude,
                            MapPoint::FIELD_LAST_LATITUDE => $drone->latitude
                        ]);

                        break;
                    }

                    $result[] = [
                        'id' => $drone->id,
                        'lgt' => (float) $item[0],
                        'lat' => (float) $item[1]
                    ];

                    $drone->update([
                        MapPoint::FIELD_LAST_LONGITUDE => (float) $item[0],
                        MapPoint::FIELD_LAST_LATITUDE => (float) $item[1]
                    ]);
                    break;
                }
            }
        }


        return response()->json($result);
    }
}
