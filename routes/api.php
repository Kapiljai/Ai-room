<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomJobController;
Route::prefix('v1')->group(function () {
    Route::post('/jobs', [RoomJobController::class, 'store']);
    Route::get('/jobs/{job}', [RoomJobController::class, 'show']);
    Route::get('/jobs/{job}/artifacts/{artifact}', [RoomJobController::class, 'artifact']);
});
