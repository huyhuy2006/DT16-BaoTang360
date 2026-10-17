<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreSpaceRequest extends FormRequest { public function authorize(): bool{return $this->user()?->isEditor()===true;} public function rules(): array{return ['name'=>['required','string','max:255'],'description'=>['nullable','string'],'cover_image'=>['nullable','image','max:5120']];}}
