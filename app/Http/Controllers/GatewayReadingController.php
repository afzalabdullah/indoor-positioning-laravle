<?php
namespace App\Http\Controllers;

use App\Models\GatewayReading;
use Illuminate\Http\Request;

class GatewayReadingController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'transmitterSerialNumber' => 'required|string',
            'nodeType' => 'required|string',
            'reads' => 'required|array',
            'reads.*.timeStampUTC' => 'required|date',
            'reads.*.deviceUID' => 'required|string',
            'reads.*.manufacturerName' => 'required|string',
            'reads.*.distance' => 'required|integer',
            'reads.*.count' => 'required|integer',
        ]);

        // Prepare an array for batch insertion
        $gatewayReadingsData = [];

        foreach ($request->reads as $read) {
            $gatewayReadingsData[] = [
                'transmitter_serial_number' => $request->transmitterSerialNumber,
                'node_type' => $request->nodeType,
                'device_uid' => $read['deviceUID'],
                'manufacturer_name' => $read['manufacturerName'],
                'distance' => $read['distance'],
                'time_stamp_utc' => $read['timeStampUTC'],
                'count' => $read['count'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all readings at once
        GatewayReading::insert($gatewayReadingsData);

        return response()->json(['message' => 'Gateway readings stored successfully.'], 201);
    }
}
