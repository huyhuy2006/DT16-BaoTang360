<?php

return [
    'python' => [
        'url' => env('PYTHON_SERVICE_URL', 'http://127.0.0.1:8001'),
        'token' => env('PYTHON_SERVICE_TOKEN', ''),
        'timeout' => (int) env('PYTHON_SERVICE_TIMEOUT', 3),
    ],
];
