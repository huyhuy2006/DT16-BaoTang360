<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:health', function () {
    $this->info('Bao Tang Ao backend is healthy.');
})->purpose('Check backend health.');
