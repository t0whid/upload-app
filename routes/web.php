<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\DownloadController;

Route::get('/', function () {
    return view('users.pages.upload');
});

Route::post('/upload', [UploadController::class, 'upload']);

Route::get('/download/{slugs}', [DownloadController::class, 'show'])->name('download.show');
Route::post('/download/verify-captcha', [DownloadController::class, 'verifyCaptcha'])->name('download.verify-captcha');
Route::get('/captcha/image', [DownloadController::class, 'getCaptcha'])->name('download.captcha');
Route::post('/captcha/refresh', [DownloadController::class, 'refreshCaptcha'])->name('download.refresh-captcha');