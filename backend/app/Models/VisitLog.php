<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'session_id', 'scene_id', 'hotspot_id',
        'action', 'duration_seconds', 'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function scene()
    {
        return $this->belongsTo(Scene::class);
    }

    public function hotspot()
    {
        return $this->belongsTo(Hotspot::class);
    }
}
