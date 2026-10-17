<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class CollectionItemResource extends JsonResource { public function toArray($request): array{return ['id'=>$this->id,'user_id'=>$this->user_id,'artifact_id'=>$this->artifact_id,'note'=>$this->note,'artifact'=>$this->whenLoaded('artifact')];}}
