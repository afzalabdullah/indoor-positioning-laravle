<?php

namespace App\Http\Controllers;

use App\Events\PositionUpdated;
use App\Models\Anchor;
use App\Models\Assets;
use App\Models\GatewayReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrilaterationController extends Controller
{
    /**
     * Fetch the latest anchors and distances, and perform trilateration.
     */
    // public function getLatestPosition(Request $request)
    // {
    //     Log::debug('Received request data:', $request->all());

    //     $request->validate([
    //         'anchors' => 'required|array',
    //         'anchors.*' => 'string',
    //     ]);

    //     Log::debug('Validated anchors:', $request->anchors);

    //     $deviceUidReadings = [];

    //     // Fetch readings for each anchor
    //     foreach ($request->anchors as $anchorUid) {
    //         $latestReadings = GatewayReading::where('transmitter_serial_number', $anchorUid)
    //             ->orderBy('time_stamp_utc', 'desc')
    //             ->get();

    //         Log::debug('Fetched latest readings for anchor UID: ' . $anchorUid, [
    //             'readings' => $latestReadings->toArray(),
    //         ]);

    //         if ($latestReadings->isEmpty()) {
    //             Log::error('No readings found for anchor UID: ' . $anchorUid);
    //             return response()->json(['error' => 'No readings found for anchor: ' . $anchorUid], 400);
    //         }

    //         foreach ($latestReadings as $latestReading) {
    //             $deviceUid = $latestReading->device_uid;
    //             $anchorData = Anchor::where('uid', $anchorUid)->first();

    //             if ($anchorData) {
    //                 // Store distance in meters
    //                 $deviceUidReadings[$deviceUid][] = [
    //                     'x' => (float) $anchorData->x,      // Anchor x-coordinate in meters
    //                     'y' => (float) $anchorData->y,      // Anchor y-coordinate in meters
    //                     'distance' => (float) $latestReading->distance / 1000.0, // Convert distance from mm to m
    //                 ];
    //             } else {
    //                 Log::error('Anchor data not found for UID: ' . $anchorUid);
    //                 return response()->json(['error' => 'Anchor not found for UID: ' . $anchorUid], 404);
    //             }
    //         }
    //     }

    //     $results = [];

    //     // Perform trilateration for each device
    //     foreach ($deviceUidReadings as $deviceUid => $anchorDistances) {
    //         if (count($anchorDistances) < 3) {
    //             $results[$deviceUid] = ['error' => 'Insufficient anchor data'];
    //             continue;
    //         }

    //         // Prepare arrays for trilateration
    //         $anchorPositions = [];
    //         $distances = [];

    //         foreach ($anchorDistances as $anchor) {
    //             $anchorPositions[] = [$anchor['x'], $anchor['y']]; // Add (x, y) coordinates
    //             $distances[] = $anchor['distance'];                // Add distance
    //         }

    //         // Log the data before performing trilateration
    //         Log::debug('Calculating position for Device UID: ' . $deviceUid, [
    //             'Anchor Positions' => $anchorPositions,
    //             'Distances' => $distances,
    //         ]);

    //         // Perform trilateration
    //         try {
    //             $position = $this->trilaterate($anchorPositions, $distances);
    //         } catch (\Exception $e) {
    //             Log::error('Trilateration error: ' . $e->getMessage());
    //             $position = ['error' => 'Trilateration error: ' . $e->getMessage()];
    //         }

    //         // Broadcast the position update event
    //         broadcast(new PositionUpdated(['deviceUid' => $deviceUid, 'position' => $position]));

    //         $results[$deviceUid] = $position; // Store calculated position
    //     }

    //     return response()->json($results); // Return results in JSON format
    // }

    public function getLatestPosition(Request $request)
{
    Log::debug('Received request data:', $request->all());

    $request->validate([
        'anchors' => 'required|array',
        'anchors.*' => 'string',
    ]);

    Log::debug('Validated anchors:', $request->anchors);

    $deviceUidReadings = [];

    // Fetch readings for each anchor
    foreach ($request->anchors as $anchorUid) {
        $latestReadings = GatewayReading::where('transmitter_serial_number', $anchorUid)
            ->orderBy('time_stamp_utc', 'desc')
            ->get();

        Log::debug('Fetched latest readings for anchor UID: ' . $anchorUid, [
            'readings' => $latestReadings->toArray(),
        ]);

        if ($latestReadings->isEmpty()) {
            Log::error('No readings found for anchor UID: ' . $anchorUid);
            return response()->json(['error' => 'No readings found for anchor: ' . $anchorUid], 400);
        }

        foreach ($latestReadings as $latestReading) {
            $deviceUid = $latestReading->device_uid;
            $anchorData = Anchor::where('uid', $anchorUid)->first();

            if ($anchorData) {
                // Store distance in meters
                $deviceUidReadings[$deviceUid][] = [
                    'x' => (float) $anchorData->x,
                    'y' => (float) $anchorData->y,
                    'distance' => (float) $latestReading->distance / 1000.0,
                ];
            } else {
                Log::error('Anchor data not found for UID: ' . $anchorUid);
                return response()->json(['error' => 'Anchor not found for UID: ' . $anchorUid], 404);
            }
        }
    }

    $results = [];

    // Perform trilateration for each device
    foreach ($deviceUidReadings as $deviceUid => $anchorDistances) {
        if (count($anchorDistances) < 3) {
            $results[$deviceUid] = ['error' => 'Insufficient anchor data'];
            continue;
        }

        // Prepare arrays for trilateration
        $anchorPositions = [];
        $distances = [];

        foreach ($anchorDistances as $anchor) {
            $anchorPositions[] = [$anchor['x'], $anchor['y']];
            $distances[] = $anchor['distance'];
        }

        // Log the data before performing trilateration
        Log::debug('Calculating position for Device UID: ' . $deviceUid, [
            'Anchor Positions' => $anchorPositions,
            'Distances' => $distances,
        ]);

        // Perform trilateration
        try {
            $position = $this->trilaterate($anchorPositions, $distances);
        } catch (\Exception $e) {
            Log::error('Trilateration error: ' . $e->getMessage());
            $position = ['error' => 'Trilateration error: ' . $e->getMessage()];
        }

        // Fetch asset info (icon and name) based on device_uid
        $asset = Assets::where('device_uid', $deviceUid)->first();
        if ($asset) {
            $results[$deviceUid] = array_merge($position, [
                'icon' => $asset->device_icon,
                'name' => $asset->device_name,
            ]);
        } else {
            $results[$deviceUid] = array_merge($position, [
                'icon' => null,
                'name' => 'Unknown Device',
            ]);
        }

        // Broadcast the position update event
        broadcast(new PositionUpdated(['deviceUid' => $deviceUid, 'position' => $position]));
    }

    return response()->json($results); // Return results in JSON format
}


    /**
     * Trilateration logic using the geometric approach.
     */
    private function trilaterate($anchorPositions, $distances)
    {
        if (count($anchorPositions) < 3) {
            throw new \Exception("Trilateration requires at least three anchor nodes.");
        }

        // Unpack anchor positions and distances for easier referencing
        list($x1, $y1) = $anchorPositions[0];
        $r1 = $distances[0];

        list($x2, $y2) = $anchorPositions[1];
        $r2 = $distances[1];

        list($x3, $y3) = $anchorPositions[2];
        $r3 = $distances[2];

        // Calculate position using trilateration formulas
        $A = 2 * ($x2 - $x1);
        $B = 2 * ($y2 - $y1);
        $C = $r1 ** 2 - $r2 ** 2 - $x1 ** 2 + $x2 ** 2 - $y1 ** 2 + $y2 ** 2;
        $D = 2 * ($x3 - $x1);
        $E = 2 * ($y3 - $y1);
        $F = $r1 ** 2 - $r3 ** 2 - $x1 ** 2 + $x3 ** 2 - $y1 ** 2 + $y3 ** 2;

        // Calculate the coordinates
        $y = ($C - $A * $F / $D) / ($B - $A * $E / $D);
        if ($D != 0) {
            $x = ($C - $B * $y) / $A;
        } else {
            throw new \Exception("Could not determine the position due to degenerate case.");
        }

        return ['x' => $x, 'y' => $y]; // Return position in meters
    }
}
