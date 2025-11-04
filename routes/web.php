<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncLabController;
use App\Http\Controllers\SendHasilLabController;

Route::get('/', function () {
    return view('test-panel');
});

// Test Panel Routes
Route::get('/test/sync-lab', [SyncLabController::class, 'syncToExternalApi']);
Route::get('/test/send-hasil-lab', [SendHasilLabController::class, 'sendHasilLab']);
Route::get('/test/send-hasil-lab-with-file', [SendHasilLabController::class, 'sendHasilLabWithFile']);
Route::get('/test/send-multiple-hasil-lab', [SendHasilLabController::class, 'sendMultipleHasilLab']);
