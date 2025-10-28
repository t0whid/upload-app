<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    // Show download page with captcha
    public function show($slugs)
    {
        $slugsArray = explode(',', $slugs);
        $files = UploadedFile::whereIn('slug', $slugsArray)->get();

        if ($files->isEmpty()) {
            return view('users.pages.expired', ['message' => 'No valid files found.']);
        }

        // 🔥 Check if any file is expired
        foreach ($files as $file) {
            if ($file->expires_at && now()->greaterThan($file->expires_at)) {
                return view('users.pages.expired', [
                    'message' => 'This download link has expired. Please re-upload your file.'
                ]);
            }
        }

        // Generate captcha code
        $captcha = strtoupper(Str::random(5));
        session(['download_captcha' => $captcha]);

        return view('users.pages.download', compact('files', 'slugs', 'captcha'));
    }

    // Generate captcha image
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

        // random lines
        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }

        // add text
        $fontSize = 5; // built-in font
        $x = 10;
        $y = 15;
        imagestring($image, $fontSize, $x, $y, $code, $textColor);

        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
        exit;
    }

    // Verify captcha and show links
    public function verify(Request $request)
    {
        $request->validate([
            'captcha' => 'required|string',
            'slugs' => 'required|string'
        ]);

        if (strtoupper($request->captcha) !== session('download_captcha')) {
            return back()->withErrors(['captcha' => 'Captcha is incorrect.'])->withInput();
        }

        $slugsArray = explode(',', $request->slugs);
        $files = UploadedFile::whereIn('slug', $slugsArray)->get();

        session()->forget('download_captcha');

        return view('users.pages.download_links', compact('files'));
    }
}
