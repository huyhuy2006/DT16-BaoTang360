<?php

namespace App\Services;

use App\Models\VisitLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class VisitAnalyticsService
{
    public function summary(): array
    {
        $logs = VisitLog::query()->latest()->limit(5000)->get([
            'scene_id', 'hotspot_id', 'action', 'duration_seconds', 'created_at',
        ]);

        $local = [
            'total_visits' => VisitLog::where('action', 'view_scene')->count(),
            'total_hotspot_clicks' => VisitLog::where('action', 'click_hotspot')->count(),
            'total_narration_plays' => VisitLog::where('action', 'play_narration')->count(),
            'unique_sessions' => VisitLog::query()->distinct('session_id')->count('session_id'),
            'top_scenes' => VisitLog::select('scene_id', DB::raw('count(*) as total'))
                ->where('action', 'view_scene')->whereNotNull('scene_id')->groupBy('scene_id')
                ->orderByDesc('total')->with('scene:id,name')->limit(10)->get(),
        ];

        $payload = [
            'actions' => $logs->map(fn ($log) => [
                'scene_id' => $log->scene_id,
                'hotspot_id' => $log->hotspot_id,
                'action' => $log->action,
                'duration_seconds' => $log->duration_seconds,
            ])->values()->all(),
        ];

        try {
            $response = Http::timeout(config('services.python.timeout', 3))
                ->acceptJson()
                ->withHeaders(array_filter([
                    'X-Service-Token' => config('services.python.token'),
                ]))
                ->post(rtrim(config('services.python.url'), '/') . '/analytics/summary', $payload);

            $local['python'] = $response->successful()
                ? ['status' => 'ok', 'summary' => $response->json()]
                : ['status' => 'fallback', 'http_status' => $response->status()];
        } catch (\Throwable $e) {
            $local['python'] = ['status' => 'fallback', 'message' => 'Python service unavailable'];
        }

        return $local;
    }
}
