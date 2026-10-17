<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreArtifactRequest extends FormRequest { public function authorize(): bool{return $this->user()?->isEditor()===true;} public function rules(): array{return ['name'=>['required','string','max:255'],'description'=>['nullable','string'],'period'=>['nullable','string','max:255'],'material'=>['nullable','string','max:255'],'image'=>['nullable','image','max:5120'],'video_url'=>['nullable','url','max:2048']];}}
