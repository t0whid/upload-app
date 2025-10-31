<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\DownloadController;

Route::get('/upload', function () {
    return view('users.pages.upload');
})->name('upload.form');

// web.php
Route::post('/upload', [UploadController::class, 'upload'])->name('upload');
Route::post('/upload/check-rate-limit', [UploadController::class, 'checkRateLimit'])->name('upload.check-rate-limit');
Route::get('/download/{slugs}', [DownloadController::class, 'show'])->name('download.show');
Route::post('/download/verify-captcha', [DownloadController::class, 'verifyCaptcha'])->name('download.verify-captcha');
Route::get('/captcha/image', [DownloadController::class, 'getCaptcha'])->name('download.captcha');
Route::post('/captcha/refresh', [DownloadController::class, 'refreshCaptcha'])->name('download.refresh-captcha');
Route::get('/download/single/{slug}', [DownloadController::class, 'showSingle'])->name('download.single');

Route::get('/', [FileController::class, 'showForm'])->name('file.form');
Route::post('/generates', [FileController::class, 'generate'])->name('file.generate');
Route::post('/generates-links', [FileController::class, 'generateLinks'])->name('file.generate-links');

Route::post('/generates-bulk', [FileController::class, 'generateBulk'])->name('file.generate-bulk');
Route::get('/downloads/{slug}', [FileController::class, 'download'])->name('file.download');
Route::post('/verify-download', [FileController::class, 'verifyAndDownload'])->name('file.verify-download');
Route::get('/bulk-display', [FileController::class, 'bulkDisplay'])->name('file.bulk-display'); // NEW
Route::get('/links-display/{batch_id?}', [FileController::class, 'linksDisplay'])->name('file.links-display');
Route::post('/verify-download-all', [FileController::class, 'verifyAndDownloadAll'])->name('file.verify-download-all');
use Illuminate\Support\Facades\Artisan;

/* Route::get('/migrate', function () {
    Artisan::call('migrate:fresh', ['--force' => true]);
    return "<h3>✅ Fresh migration completed successfully!</h3>";
}); */