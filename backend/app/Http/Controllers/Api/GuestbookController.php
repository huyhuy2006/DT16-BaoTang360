<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestbookRequest;
use App\Models\GuestbookEntry;
use Illuminate\Http\Request;
class GuestbookController extends Controller { public function index(){return GuestbookEntry::latest()->paginate(15);} public function store(StoreGuestbookRequest $request){$data=$request->validated();$entry=GuestbookEntry::create(['user_id'=>$request->user()?->id,'display_name'=>$data['display_name'],'message'=>$data['message']]);return response()->json($entry,201);}}
