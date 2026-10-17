<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artifact extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'period', 'material', 'image', 'video_url'];

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class);
    }

    public function narrations()
    {
        return $this->hasMany(Narration::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'target_id')->where('target_type', 'artifact');
    }
}
