<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArtifactRequest;
use App\Models\Artifact;
use Illuminate\Http\Request;
class ArtifactController extends Controller {
 public function index(Request $request){$query=Artifact::query()->with('narrations.language');if($request->filled('q'))$query->where('name','like','%'.$request->string('q').'%');return $query->orderBy('name')->paginate(20);}
 public function show(Artifact $artifact){return $artifact->load('narrations.language');}
 public function store(StoreArtifactRequest $request){$data=$request->validated();$data['image']=$request->hasFile('image')?$request->file('image')->store('artifacts','public'):null;return response()->json(Artifact::create($data),201);}
 public function update(Request $request, Artifact $artifact){$data=$request->validate(['name'=>['sometimes','string','max:255'],'description'=>['nullable','string'],'period'=>['nullable','string','max:255'],'material'=>['nullable','string','max:255'],'image'=>['nullable','image','max:5120'],'video_url'=>['nullable','url','max:2048']]);if($request->hasFile('image'))$data['image']=$request->file('image')->store('artifacts','public');$artifact->update($data);return response()->json($artifact);}
 public function destroy(Artifact $artifact){$artifact->delete();return response()->json(['message'=>'Đã xoá hiện vật.']);}
}
