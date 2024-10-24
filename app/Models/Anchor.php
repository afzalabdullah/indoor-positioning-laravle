<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anchor extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'uid', 'x', 'y'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
