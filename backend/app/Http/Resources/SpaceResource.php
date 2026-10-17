<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class SpaceResource extends JsonResource { public function toArray($request): array{return ['id'=>$this->id,'name'=>$this->name,'slug'=>$this->slug,'description'=>$this->description,'cover_image'=>$this->cover_image,'scenes'=>$this->whenLoaded('scenes')];}}
