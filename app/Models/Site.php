<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'name', 'description', 'assets', 'image_url'];

    public function anchors()
    {
        return $this->hasMany(Anchor::class);
    }
    public function assets()
    {
        return $this->hasMany(Assets::class); // Adjust this according to your Asset model
    }

}
