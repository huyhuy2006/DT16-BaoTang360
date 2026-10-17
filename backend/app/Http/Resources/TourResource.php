<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class TourResource extends JsonResource { public function toArray($request): array{return ['id'=>$this->id,'name'=>$this->name,'theme'=>$this->theme,'description'=>$this->description,'cover_image'=>$this->cover_image,'steps'=>$this->whenLoaded('steps')];}}
