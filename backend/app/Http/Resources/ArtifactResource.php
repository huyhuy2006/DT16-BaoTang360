<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class ArtifactResource extends JsonResource { public function toArray($request): array{return ['id'=>$this->id,'name'=>$this->name,'description'=>$this->description,'period'=>$this->period,'material'=>$this->material,'image'=>$this->image];}}
