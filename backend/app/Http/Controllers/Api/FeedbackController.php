<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\Feedback;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FeedbackController extends Controller
{
    /** Public listing returns approved feedback only. */
    public function index(Request $request)
    {
        $query = Feedback::query()->with('user:id,name')->where('status', 'approved');

        if ($request->filled('target_type')) {
            $query->where('target_type', $request->string('target_type')->toString());
        }
        if ($request->filled('target_id')) {
            $query->where('target_id', $request->integer('target_id'));
        }

        return $query->latest()->paginate(15);
    }

    public function mine(Request $request)
    {
        return $request->user()->feedback()->latest()->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'target_type' => ['required', 'in:artifact,tour'],
            'target_id' => ['required', 'integer', 'min:1'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $exists = $data['target_type'] === 'tour'
            ? Tour::query()->whereKey($data['target_id'])->exists()
            : Artifact::query()->whereKey($data['target_id'])->exists();

        if (! $exists) {
            throw ValidationException::withMessages(['target_id' => 'Không tìm thấy tour hoặc hiện vật được đánh giá.']);
        }

        // Each account may keep one current review per target; a new submission
        // returns to pending moderation so edits are reviewed as well.
        $feedback = Feedback::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'target_type' => $data['target_type'],
                'target_id' => $data['target_id'],
            ],
            [
                'rating' => $data['rating'],
                'comment' => trim($data['comment']),
                'status' => 'pending',
            ]
        );

        return response()->json([
            'message' => 'Đã ghi nhận đánh giá. Nội dung sẽ hiển thị công khai sau khi được kiểm duyệt.',
            'feedback' => $feedback,
        ], $feedback->wasRecentlyCreated ? 201 : 200);
    }

    public function adminIndex()
    {
        return Feedback::query()->with('user:id,name')->latest()->paginate(30);
    }

    public function moderate(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $feedback->update(['status' => $data['status']]);

        return response()->json($feedback->load('user:id,name'));
    }
}
