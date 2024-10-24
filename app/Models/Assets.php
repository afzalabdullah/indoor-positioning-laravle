<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    use HasFactory;

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'device_uid',   // Unique Device Name
        'device_icon',  // Device Icon URL
        'device_name',  // Device Name
        'site_id',      // Foreign key for the sites table
    ];

    // Define the relationship with the Site model
    public function site()
    {
        return $this->belongsTo(Site::class); // An asset belongs to a site
    }
}
