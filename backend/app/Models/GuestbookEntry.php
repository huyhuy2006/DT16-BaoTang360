<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestbookEntry extends Model
{
    protected $fillable = ['user_id', 'display_name', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
