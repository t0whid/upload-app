<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\DownloadController;

Route::get('/', function () {
    return view('users.pages.upload');
})->name('upload.form');

// web.php
Route::post('/upload', [UploadController::class, 'upload'])->name('upload');
Route::post('/upload/check-rate-limit', [UploadController::class, 'checkRateLimit'])->name('upload.check-rate-limit');
Route::get('/download/{slugs}', [DownloadController::class, 'show'])->name('download.show');
Route::post('/download/verify-captcha', [DownloadController::class, 'verifyCaptcha'])->name('download.verify-captcha');
Route::get('/captcha/image', [DownloadController::class, 'getCaptcha'])->name('download.captcha');
Route::post('/captcha/refresh', [DownloadController::class, 'refreshCaptcha'])->name('download.refresh-captcha');
