<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class SceneResource extends JsonResource { public function toArray($request): array{return ['id'=>$this->id,'space_id'=>$this->space_id,'name'=>$this->name,'panorama_image'=>$this->panorama_image,'initial_pitch'=>$this->initial_pitch,'initial_yaw'=>$this->initial_yaw,'initial_hfov'=>$this->initial_hfov,'order_index'=>$this->order_index,'hotspots'=>$this->whenLoaded('hotspots'),'outgoing_links'=>$this->whenLoaded('outgoingLinks')];}}
