<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Models\Tour;
use App\Services\TourService;
class TourController extends Controller {
 public function __construct(private TourService $tourService) {}
 public function index(){return Tour::withCount('steps')->orderBy('name')->get();}
 public function show(Tour $tour){return $tour->load('steps.scene.hotspots.artifact.narrations.language','steps.scene.outgoingLinks.toScene');}
 public function store(StoreTourRequest $request){$data=$request->validated();if($request->hasFile('cover_image'))$data['cover_image']=$request->file('cover_image')->store('tours','public');$tour=$this->tourService->create($data,$request->user()->id);return response()->json($tour,201);}
 public function update(StoreTourRequest $request, Tour $tour){$data=$request->validated();if($request->hasFile('cover_image'))$data['cover_image']=$request->file('cover_image')->store('tours','public');$tour=$this->tourService->update($data,$tour);return response()->json($tour);}
 public function destroy(Tour $tour){$tour->delete();return response()->json(['message'=>'Đã xoá lộ trình.']);}
}
