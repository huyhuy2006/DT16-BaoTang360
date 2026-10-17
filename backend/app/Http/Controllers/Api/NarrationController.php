<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNarrationRequest;
use App\Models\Narration;
use Illuminate\Http\Request;
class NarrationController extends Controller {
 public function index(Request $request){$query=Narration::with('language');if($request->filled('hotspot_id'))$query->where('hotspot_id',$request->integer('hotspot_id'));if($request->filled('artifact_id'))$query->where('artifact_id',$request->integer('artifact_id'));return $query->get();}
 public function store(StoreNarrationRequest $request){$data=$request->validated();$path=$request->file('audio')->store('narrations','public');$n= Narration::create(['hotspot_id'=>$data['hotspot_id']??null,'artifact_id'=>$data['artifact_id']??null,'language_id'=>$data['language_id'],'audio_path'=>$path,'transcript'=>$data['transcript']??null]);return response()->json($n->load('language'),201);}
 public function destroy(Narration $narration){$narration->delete();return response()->json(['message'=>'Đã xoá bản thuyết minh.']);}
}
