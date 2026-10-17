<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreVisitLogRequest extends FormRequest { public function authorize(): bool{return true;} public function rules(): array{return ['session_id'=>['required','string','max:100'],'scene_id'=>['nullable','exists:scenes,id'],'hotspot_id'=>['nullable','exists:hotspots,id'],'action'=>['required','in:view_scene,click_hotspot,play_narration,start_tour,finish_tour'],'duration_seconds'=>['nullable','integer','min:0']];}}
