<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\DownloadController;

Route::get('/', function () {
    return view('upload');
});

Route::post('/upload', [UploadController::class, 'upload']);
Route::get('/download/{slug}', [DownloadController::class, 'show'])->name('download.show');
