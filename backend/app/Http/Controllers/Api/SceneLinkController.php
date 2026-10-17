<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSceneLinkRequest;
use App\Models\SceneLink;
class SceneLinkController extends Controller { public function store(StoreSceneLinkRequest $request){$data=$request->validated();$link=SceneLink::firstOrCreate(['from_scene_id'=>$data['from_scene_id'],'to_scene_id'=>$data['to_scene_id']],$data);return response()->json($link->load('toScene'),$link->wasRecentlyCreated?201:200);} public function destroy(SceneLink $sceneLink){$sceneLink->delete();return response()->json(['message'=>'Đã xoá liên kết chuyển cảnh.']);}}
