<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCollectionItemRequest;
use App\Models\CollectionItem;
use Illuminate\Http\Request;
class CollectionItemController extends Controller { public function index(Request $request){return $request->user()->collectionItems()->with('artifact')->latest()->get();} public function store(StoreCollectionItemRequest $request){$data=$request->validated();$item=CollectionItem::updateOrCreate(['user_id'=>$request->user()->id,'artifact_id'=>$data['artifact_id']],['note'=>$data['note']??null]);return response()->json($item->load('artifact'),$item->wasRecentlyCreated?201:200);} public function destroy(Request $request, CollectionItem $collectionItem){abort_unless($collectionItem->user_id===$request->user()->id,403,'Không thể xoá mục của người dùng khác.');$collectionItem->delete();return response()->json(['message'=>'Đã xoá khỏi bộ sưu tập.']);}}
