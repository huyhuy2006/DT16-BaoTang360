<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\Favorite;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->latest()->get();

        return response()->json($favorites->map(function (Favorite $favorite): array {
            $target = $favorite->target_type === 'tour'
                ? Tour::query()->select('id', 'name', 'theme', 'description', 'cover_image')->find($favorite->target_id)
                : Artifact::query()->select('id', 'name', 'description', 'period', 'material', 'image', 'video_url')->find($favorite->target_id);

            return [
                'id' => $favorite->id,
                'target_type' => $favorite->target_type,
                'target_id' => $favorite->target_id,
                'target' => $target,
                'created_at' => $favorite->created_at,
            ];
        })->filter(fn (array $item): bool => $item['target'] !== null)->values());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'target_type' => ['required', 'in:artifact,tour'],
            'target_id' => ['required', 'integer', 'min:1'],
        ]);

        $exists = $data['target_type'] === 'tour'
            ? Tour::query()->whereKey($data['target_id'])->exists()
            : Artifact::query()->whereKey($data['target_id'])->exists();

        if (! $exists) {
            throw ValidationException::withMessages(['target_id' => 'Đối tượng yêu thích không tồn tại.']);
        }

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'],
        ]);

        return response()->json([
            'id' => $favorite->id,
            'target_type' => $favorite->target_type,
            'target_id' => $favorite->target_id,
        ], $favorite->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, string $targetType, int $targetId)
    {
        abort_unless(in_array($targetType, ['artifact', 'tour'], true), 404);

        $deleted = $request->user()->favorites()
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->delete();

        return response()->json(['deleted' => $deleted > 0]);
    }
}
