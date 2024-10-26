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
    public function getLatestPosition(Request $request)
    {
        Log::debug('Received request data:', $request->all());

        $request->validate([
            'anchors' => 'required|array',
            'anchors.*' => 'string',
        ]);

        Log::debug('Validated anchors:', $request->anchors);

        // This array will hold readings grouped by device UID.
        $deviceUidReadings = [];

        // Fetch the latest readings for each anchor based on transmitter_serial_number.
        $allReadings = GatewayReading::whereIn('transmitter_serial_number', $request->anchors)
            ->orderBy('time_stamp_utc', 'desc')
            ->get();

        foreach ($allReadings as $latestReading) {
            $deviceUid = $latestReading->device_uid; // Get device UID for this reading.
            $anchorData = Anchor::where('uid', $latestReading->transmitter_serial_number)->first(); // Fetch anchor coordinates.

            if ($anchorData) {
                // Store distance in meters for trilateration.
                $deviceUidReadings[$deviceUid][] = [
                    'x' => (float) $anchorData->x, // Anchor x-coordinate
                    'y' => (float) $anchorData->y, // Anchor y-coordinate
                    'distance' => (float) $latestReading->distance / 1000.0, // Convert mm to meters
                ];
            } else {
                Log::error('Anchor data not found for UID: ' . $latestReading->transmitter_serial_number);
                return response()->json(['error' => 'Anchor not found for UID: ' . $latestReading->transmitter_serial_number], 404);
            }
        }

        // Log the final state of readings grouped by device UID
        Log::debug('Final device UID readings:', $deviceUidReadings);

        $results = [];

        // Perform trilateration for each device based on the gathered readings.
        foreach ($deviceUidReadings as $deviceUid => $anchorDistances) {
            if (count($anchorDistances) < 3) {
                $results[$deviceUid] = ['error' => 'Insufficient anchor data'];
                Log::warning('Insufficient anchor data for Device UID: ' . $deviceUid);
                continue; // Skip trilateration if not enough data
            }

            $anchorPositions = [];
            $distances = [];

            // Prepare the anchor positions and distances for trilateration.
            foreach ($anchorDistances as $anchor) {
                $anchorPositions[] = [$anchor['x'], $anchor['y']];
                $distances[] = $anchor['distance'];
            }

            Log::debug('Calculating position for Device UID: ' . $deviceUid, [
                'Anchor Positions' => $anchorPositions,
                'Distances' => $distances,
            ]);

            // Perform trilateration.
            try {
                $position = $this->trilaterate($anchorPositions, $distances);
            } catch (\Exception $e) {
                Log::error('Trilateration error for Device UID ' . $deviceUid . ': ' . $e->getMessage());
                $position = ['error' => 'Trilateration error: ' . $e->getMessage()];
            }

            // Fetch asset info (icon and name) based on device_uid.
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

            // Broadcast the position update event.
            broadcast(new PositionUpdated(['deviceUid' => $deviceUid, 'position' => $position]));
        }

        return response()->json($results); // Return results in JSON format.
    }

    /**
     * Trilateration logic using the geometric approach.
     */
    public function trilaterate(array $anchorPositions, array $distances)
    {
        // Step 1: Validate input data
        if (count($anchorPositions) < 3) {
            throw new \Exception("At least three unique anchor positions are required.");
        }

        if (count($anchorPositions) !== count($distances)) {
            throw new \Exception("The number of distances must match the number of anchor positions.");
        }

        // Ensure all anchors are unique and distances are valid
        $uniquePositions = array_unique($anchorPositions, SORT_REGULAR);
        if (count($uniquePositions) < 3) {
            throw new \Exception("Anchor positions must be unique.");
        }

        foreach ($distances as $distance) {
            if ($distance <= 0) {
                throw new \Exception("Distances must be greater than zero.");
            }
        }

        // Step 2: Extract coordinates for easier calculation
        list($x1, $y1) = $anchorPositions[0];
        list($x2, $y2) = $anchorPositions[1];
        list($x3, $y3) = $anchorPositions[2];

        $d1 = $distances[0];
        $d2 = $distances[1];
        $d3 = $distances[2];

        // Step 3: Calculate intermediate values to avoid division by zero
        $A = 2 * ($x2 - $x1);
        $B = 2 * ($y2 - $y1);
        $C = 2 * ($x3 - $x2);
        $D = 2 * ($y3 - $y2);

        $E = pow($d1, 2) - pow($d2, 2) - pow($x1, 2) + pow($x2, 2) - pow($y1, 2) + pow($y2, 2);
        $F = pow($d2, 2) - pow($d3, 2) - pow($x2, 2) + pow($x3, 2) - pow($y2, 2) + pow($y3, 2);

        // Check for division by zero (parallel lines or identical points)
        if (($A * $D - $B * $C) == 0) {
            throw new \DivisionByZeroError("Invalid anchor configuration: Division by zero detected.");
        }

        // Step 4: Calculate the x and y coordinates of the target position
        $x = ($E * $D - $B * $F) / ($A * $D - $B * $C);
        $y = ($A * $F - $E * $C) / ($A * $D - $B * $C);

        // Return the calculated position
        return [$x, $y];
    }
}
