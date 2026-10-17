<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class GuestbookEntryResource extends JsonResource { public function toArray($request): array{return ['id'=>$this->id,'user_id'=>$this->user_id,'display_name'=>$this->display_name,'message'=>$this->message,'created_at'=>$this->created_at];}}
