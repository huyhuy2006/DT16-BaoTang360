<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = ['name', 'theme', 'description', 'cover_image', 'created_by'];

    public function steps()
    {
        return $this->hasMany(TourStep::class)->orderBy('order_index');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function progress()
    {
        return $this->hasMany(TourProgress::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'target_id')->where('target_type', 'tour');
    }
}
