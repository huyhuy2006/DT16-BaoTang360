<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SceneLink extends Model
{
    protected $fillable = ['from_scene_id', 'to_scene_id', 'pitch', 'yaw', 'label'];

    public function fromScene()
    {
        return $this->belongsTo(Scene::class, 'from_scene_id');
    }

    public function toScene()
    {
        return $this->belongsTo(Scene::class, 'to_scene_id');
    }
}
