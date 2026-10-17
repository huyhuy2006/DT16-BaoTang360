<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotspotRequest;
use App\Models\Hotspot;
use Illuminate\Http\Request;
class HotspotController extends Controller {
 public function store(StoreHotspotRequest $request){$hotspot=Hotspot::create($request->validated());return response()->json($hotspot->load('artifact'),201);}
 public function update(Request $request, Hotspot $hotspot){$data=$request->validate(['artifact_id'=>['nullable','exists:artifacts,id'],'pitch'=>['sometimes','numeric','between:-90,90'],'yaw'=>['sometimes','numeric','between:-360,360'],'title'=>['sometimes','string','max:255']]);$hotspot->update($data);return response()->json($hotspot->load('artifact'));}
 public function destroy(Hotspot $hotspot){$hotspot->delete();return response()->json(['message'=>'Đã xoá điểm nóng.']);}
}
