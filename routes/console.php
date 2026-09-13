<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('room-ai:health', function () {
    $this->info('Room AI service is healthy.');
})->purpose('Check Room AI application health');
