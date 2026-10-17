<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreSceneRequest extends FormRequest { public function authorize(): bool{return $this->user()?->isEditor()===true;} public function rules(): array{return ['space_id'=>['required','exists:spaces,id'],'name'=>['required','string','max:255'],'panorama_image'=>['required','image','max:20480'],'initial_pitch'=>['nullable','numeric'],'initial_yaw'=>['nullable','numeric'],'initial_hfov'=>['nullable','numeric'],'order_index'=>['nullable','integer','min:0']];}}
