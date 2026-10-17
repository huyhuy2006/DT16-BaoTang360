<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreHotspotRequest extends FormRequest { public function authorize(): bool{return $this->user()?->isEditor()===true;} public function rules(): array{return ['scene_id'=>['required','exists:scenes,id'],'artifact_id'=>['nullable','exists:artifacts,id'],'pitch'=>['required','numeric','between:-90,90'],'yaw'=>['required','numeric','between:-360,360'],'title'=>['required','string','max:255']];}}
