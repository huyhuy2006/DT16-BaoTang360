<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
class ChangePasswordRequest extends FormRequest { public function authorize(): bool{return (bool)$this->user();} public function rules(): array{return ['current_password'=>['required'],'password'=>['required','confirmed',Rules\Password::min(8)]];}}
