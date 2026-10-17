<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scene extends Model
{
    use HasFactory;
    protected $fillable = [
        'space_id', 'name', 'panorama_image',
        'initial_pitch', 'initial_yaw', 'initial_hfov', 'order_index',
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class);
    }

    public function outgoingLinks()
    {
        return $this->hasMany(SceneLink::class, 'from_scene_id');
    }

    public function incomingLinks()
    {
        return $this->hasMany(SceneLink::class, 'to_scene_id');
    }

    public function tourSteps()
    {
        return $this->hasMany(TourStep::class);
    }
}
