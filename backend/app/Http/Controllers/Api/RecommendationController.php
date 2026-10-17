<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    public function index()
    {
        $topics = Tour::query()->whereNotNull('theme')->pluck('theme')->filter()->unique()->values()->all();

        try {
            $response = Http::timeout(config('services.python.timeout', 3))
                ->acceptJson()
                ->withHeaders(array_filter(['X-Service-Token' => config('services.python.token')]))
                ->post(rtrim(config('services.python.url'), '/') . '/recommendations', [
                    'topics' => $topics,
                    'limit' => 5,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'engine' => 'python',
                    'recommendations' => $response->json('recommendations', []),
                ]);
            }

            Log::warning('Python recommendation service returned a non-success response', ['status' => $response->status()]);
        } catch (\Throwable $e) {
            Log::warning('Python recommendation service unavailable; using fallback.');
        }

        // Safe fallback keeps the public tour list usable if Python is offline.
        return response()->json([
            'engine' => 'fallback',
            'recommendations' => array_slice($topics, 0, 5),
        ]);
    }
}
