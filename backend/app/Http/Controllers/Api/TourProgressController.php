<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourProgress;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TourProgressController extends Controller
{
    public function show(Request $request, Tour $tour)
    {
        $progress = $request->user()->tourProgresses()->where('tour_id', $tour->id)->first();
        return response()->json($progress);
    }

    public function update(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'current_step_order' => ['required', 'integer', 'min:0'],
            'visited_scene_ids' => ['nullable', 'array', 'max:500'],
            'visited_scene_ids.*' => ['integer', 'exists:scenes,id'],
            'completed' => ['sometimes', 'boolean'],
        ]);

        $stepCount = $tour->steps()->count();
        if ($stepCount === 0 || $data['current_step_order'] >= $stepCount) {
            throw ValidationException::withMessages([
                'current_step_order' => 'Vị trí hiện tại không nằm trong lộ trình hợp lệ.',
            ]);
        }

        if (($data['completed'] ?? false) && (int) $data['current_step_order'] !== $stepCount - 1) {
            throw ValidationException::withMessages(['completed' => 'Chỉ có thể hoàn thành tour tại điểm dừng cuối.']);
        }

        $allowedSceneIds = $tour->steps()->pluck('scene_id')->map(fn ($id) => (int) $id)->all();
        $visitedSceneIds = array_values(array_unique(array_map('intval', $data['visited_scene_ids'] ?? [])));
        if (array_diff($visitedSceneIds, $allowedSceneIds)) {
            throw ValidationException::withMessages(['visited_scene_ids' => 'Danh sách điểm đã tham quan chứa cảnh không thuộc tour này.']);
        }

        $progress = TourProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'tour_id' => $tour->id],
            [
                'current_step_order' => $data['current_step_order'],
                'visited_scene_ids' => $visitedSceneIds,
                'completed_at' => ($data['completed'] ?? false) ? now() : null,
            ]
        );

        return response()->json($progress);
    }
}
