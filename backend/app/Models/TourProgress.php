<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourProgress extends Model
{
    protected $table = 'tour_progress';

    protected $fillable = ['user_id', 'tour_id', 'current_step_order', 'visited_scene_ids', 'completed_at'];

    protected function casts(): array
    {
        return [
            'current_step_order' => 'integer',
            'visited_scene_ids' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
