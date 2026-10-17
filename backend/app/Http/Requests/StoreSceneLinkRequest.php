<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreSceneLinkRequest extends FormRequest { public function authorize(): bool{return $this->user()?->isEditor()===true;} public function rules(): array{return ['from_scene_id'=>['required','exists:scenes,id'],'to_scene_id'=>['required','exists:scenes,id','different:from_scene_id'],'pitch'=>['required','numeric','between:-90,90'],'yaw'=>['required','numeric','between:-360,360'],'label'=>['nullable','string','max:255']];}}
