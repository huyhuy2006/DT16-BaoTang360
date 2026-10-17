<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class NarrationResource extends JsonResource { public function toArray($request): array{return ['id'=>$this->id,'hotspot_id'=>$this->hotspot_id,'artifact_id'=>$this->artifact_id,'language_id'=>$this->language_id,'audio_path'=>$this->audio_path,'transcript'=>$this->transcript,'language'=>$this->whenLoaded('language')];}}
