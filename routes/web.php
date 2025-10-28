<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\DownloadController;

Route::get('/', function () {
    return view('users.pages.upload');
});

Route::post('/upload', [UploadController::class, 'upload']);
Route::get('/download/{slugs}', [DownloadController::class, 'show'])->name('download.show');
Route::get('/captcha.png', [DownloadController::class, 'captchaImage'])->name('download.captcha');
Route::get('/captcha/refresh', [DownloadController::class, 'refreshCaptcha'])->name('download.refresh');
Route::post('/download/verify', [DownloadController::class, 'verify'])->name('download.verify');

