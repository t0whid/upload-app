<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function show(Request $request, $slugs)
    {
        $slugsArray = explode(',', $slugs);
        $files = UploadedFile::whereIn('slug', $slugsArray)->get();

        if ($files->isEmpty()) {
            return view('users.pages.expired', ['message' => 'No valid files found.']);
        }

        foreach ($files as $file) {
            if ($file->expires_at && now()->greaterThan($file->expires_at)) {
                return view('users.pages.expired', [
                    'message' => 'This download link has expired. Please re-upload your file.'
                ]);
            }
        }

        if ($request->isMethod('post')) {
            $request->validate(['captcha' => 'required|string']);
            if (strtoupper($request->captcha) !== session('download_captcha')) {
                return back()->withErrors(['captcha' => 'Captcha is incorrect.'])->withInput();
            }

            session()->forget('download_captcha');
            return view('users.pages.download_links', compact('files'));
        }

        // GET => show captcha
        $captcha = strtoupper(Str::random(5));
        session(['download_captcha' => $captcha]);

        return view('users.pages.download', compact('files', 'slugs', 'captcha'));
    }

    public function captchaImage()
    {
        $code = session('download_captcha', 'XXXXX');
        $width = 150;
        $height = 50;
        $image = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($image, 240, 240, 240);
        $textColor = imagecolorallocate($image, 50, 50, 50);
        $lineColor = imagecolorallocate($image, 100, 100, 100);
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), $lineColor);
        }

        imagestring($image, 5, 10, 15, $code, $textColor);
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
        exit;
    }

    public function refreshCaptcha()
    {
        session(['download_captcha' => strtoupper(Str::random(5))]);
        return response()->json(['success' => true]);
    }
}
