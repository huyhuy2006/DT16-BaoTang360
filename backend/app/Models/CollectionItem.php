<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'artifact_id', 'note'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artifact()
    {
        return $this->belongsTo(Artifact::class);
    }
}
