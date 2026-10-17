<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpaceRequest;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class SpaceController extends Controller {
 public function index(){return Space::with('scenes')->orderBy('name')->get();}
 public function show(Space $space){return $space->load(['scenes.hotspots.artifact.narrations.language','scenes.outgoingLinks.toScene']);}
 public function store(StoreSpaceRequest $request){$data=$request->validated();$path=$request->hasFile('cover_image')?$request->file('cover_image')->store('spaces','public'):null;$space=Space::create(['name'=>$data['name'],'slug'=>Str::slug($data['name']).'-'.Str::lower(Str::random(8)),'description'=>$data['description']??null,'cover_image'=>$path,'created_by'=>$request->user()->id]);return response()->json($space,201);}
 public function update(Request $request, Space $space){$data=$request->validate(['name'=>['sometimes','string','max:255'],'description'=>['nullable','string'],'cover_image'=>['nullable','image','max:5120']]);if($request->hasFile('cover_image'))$data['cover_image']=$request->file('cover_image')->store('spaces','public');$space->update($data);return response()->json($space);}
 public function destroy(Space $space){$space->delete();return response()->json(['message'=>'Đã xoá không gian tham quan.']);}
}
