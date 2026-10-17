<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourStep;
use Illuminate\Support\Facades\DB;

class TourService
{
    public function create(array $data, int $userId): Tour
    {
        return DB::transaction(function () use ($data, $userId): Tour {
            $tour = Tour::create([
                'name' => $data['name'],
                'theme' => $data['theme'] ?? null,
                'description' => $data['description'] ?? null,
                'cover_image' => $data['cover_image'] ?? null,
                'created_by' => $userId,
            ]);

            $this->syncSteps($tour, $data['scene_ids']);
            return $tour->load('steps.scene');
        });
    }

    public function update(array $data, Tour $tour): Tour
    {
        return DB::transaction(function () use ($data, $tour): Tour {
            $tour->update([
                'name' => $data['name'],
                'theme' => $data['theme'] ?? null,
                'description' => $data['description'] ?? null,
                'cover_image' => $data['cover_image'] ?? $tour->cover_image,
            ]);

            $tour->steps()->delete();
            $this->syncSteps($tour, $data['scene_ids']);
            return $tour->fresh()->load('steps.scene');
        });
    }

    private function syncSteps(Tour $tour, array $sceneIds): void
    {
        foreach (array_values($sceneIds) as $index => $sceneId) {
            TourStep::create([
                'tour_id' => $tour->id,
                'scene_id' => $sceneId,
                'order_index' => $index,
            ]);
        }
    }
}
