<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreCollectionItemRequest extends FormRequest { public function authorize(): bool{return (bool)$this->user();} public function rules(): array{return ['artifact_id'=>['required','exists:artifacts,id'],'note'=>['nullable','string','max:500']];}}
