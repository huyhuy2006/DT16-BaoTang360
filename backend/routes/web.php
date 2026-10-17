<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'name' => 'Bao Tang Ao API',
    'status' => 'ok',
]));

Route::get('/healthz', function () {
    $status = [
        'application' => 'ok',
        'database' => 'unknown',
        'python_service' => 'unknown',
        'checked_at' => now()->toIso8601String(),
    ];

    try {
        DB::select('SELECT 1');
        $status['database'] = 'ok';
    } catch (\Throwable $exception) {
        $status['database'] = 'unavailable';
        return response()->json($status, 503);
    }

    try {
        $python = Http::timeout(2)
            ->withHeaders(array_filter(['X-Service-Token' => config('services.python.token')]))
            ->get(rtrim(config('services.python.url'), '/') . '/healthz');
        $status['python_service'] = $python->successful() ? 'ok' : 'unavailable';
    } catch (\Throwable $exception) {
        // Do not expose internal hostnames, secrets, or exception messages publicly.
        $status['python_service'] = 'unavailable';
    }

    return response()->json($status, 200);
});
