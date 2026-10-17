<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSceneRequest;
use App\Models\Scene;
use Illuminate\Http\Request;
class SceneController extends Controller {
 public function index(Request $request){$query=Scene::with(['hotspots.artifact.narrations.language','outgoingLinks.toScene']);if($request->filled('space_id'))$query->where('space_id',$request->integer('space_id'));return $query->orderBy('order_index')->get();}
 public function show(Scene $scene){return $scene->load(['hotspots.artifact.narrations.language','hotspots.narrations.language','outgoingLinks.toScene']);}
 public function store(StoreSceneRequest $request){$data=$request->validated();$path=$request->file('panorama_image')->store('panoramas','public');$scene=Scene::create(['space_id'=>$data['space_id'],'name'=>$data['name'],'panorama_image'=>$path,'initial_pitch'=>$data['initial_pitch']??0,'initial_yaw'=>$data['initial_yaw']??0,'initial_hfov'=>$data['initial_hfov']??100,'order_index'=>$data['order_index']??0]);return response()->json($scene,201);}
 public function update(Request $request, Scene $scene){$data=$request->validate(['name'=>['sometimes','string','max:255'],'panorama_image'=>['nullable','image','max:20480'],'initial_pitch'=>['nullable','numeric'],'initial_yaw'=>['nullable','numeric'],'initial_hfov'=>['nullable','numeric'],'order_index'=>['nullable','integer','min:0']]);if($request->hasFile('panorama_image'))$data['panorama_image']=$request->file('panorama_image')->store('panoramas','public');$scene->update($data);return response()->json($scene);}
 public function destroy(Scene $scene){$scene->delete();return response()->json(['message'=>'Đã xoá điểm nhìn.']);}
}
