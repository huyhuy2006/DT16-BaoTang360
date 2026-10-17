<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVisitLogRequest;
use App\Models\VisitLog;
use App\Services\VisitAnalyticsService;
class VisitLogController extends Controller {
 public function __construct(private VisitAnalyticsService $analytics) {}
 public function store(StoreVisitLogRequest $request){$data=$request->validated();$log=VisitLog::create([...$data,'user_id'=>$request->user()?->id,'created_at'=>now()]);return response()->json($log,201);}
 public function summary(){return response()->json($this->analytics->summary());}
}
