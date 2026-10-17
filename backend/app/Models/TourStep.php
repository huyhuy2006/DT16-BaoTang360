<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourStep extends Model
{
    protected $fillable = ['tour_id', 'scene_id', 'order_index', 'note'];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function scene()
    {
        return $this->belongsTo(Scene::class);
    }
}
