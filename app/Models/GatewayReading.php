<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'transmitter_serial_number',
        'node_type',
        'device_uid',
        'manufacturer_name',
        'distance',
        'time_stamp_utc',
        'count',
    ];
}

