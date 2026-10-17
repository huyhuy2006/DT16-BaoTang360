<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreTourRequest extends FormRequest { public function authorize(): bool{return $this->user()?->isEditor()===true;} public function rules(): array{return ['name'=>['required','string','max:255'],'theme'=>['nullable','string','max:255'],'description'=>['nullable','string'],'cover_image'=>['nullable','image','max:5120'],'scene_ids'=>['required','array','min:2'],'scene_ids.*'=>['integer','exists:scenes,id']];}}
