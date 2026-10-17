<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    protected $fillable = ['scene_id', 'artifact_id', 'pitch', 'yaw', 'title'];

    public function scene()
    {
        return $this->belongsTo(Scene::class);
    }

    public function artifact()
    {
        return $this->belongsTo(Artifact::class);
    }

    public function narrations()
    {
        return $this->hasMany(Narration::class);
    }
}
