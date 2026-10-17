<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Narration extends Model
{
    protected $fillable = ['hotspot_id', 'artifact_id', 'language_id', 'audio_path', 'transcript'];

    public function hotspot()
    {
        return $this->belongsTo(Hotspot::class);
    }

    public function artifact()
    {
        return $this->belongsTo(Artifact::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
