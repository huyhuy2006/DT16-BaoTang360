<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreNarrationRequest extends FormRequest { public function authorize(): bool{return $this->user()?->isEditor()===true;} public function rules(): array{return ['hotspot_id'=>['nullable','exists:hotspots,id'],'artifact_id'=>['nullable','exists:artifacts,id'],'language_id'=>['required','exists:languages,id'],'audio'=>['required','file','mimes:mp3,wav,m4a','max:20480'],'transcript'=>['nullable','string']];} public function withValidator($validator): void{$validator->after(function($v){if(!$this->filled('hotspot_id')&&!$this->filled('artifact_id')){$v->errors()->add('target','Phải gắn thuyết minh với hotspot hoặc hiện vật.');}});}}
