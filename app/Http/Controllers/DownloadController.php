<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class DownloadController extends Controller
{
    public function show($slugs)
    {
        $slugsArray = explode(',', $slugs);
        $files = UploadedFile::whereIn('slug', $slugsArray)->get();

        if ($files->isEmpty()) {
            return view('users.pages.expired', ['message' => 'No valid files found.']);
        }

        // Check each file if it exists on 1Fichier
        foreach ($files as $file) {
            if (!$this->checkFileExists($file)) {
                return view('users.pages.expired', [
                    'message' => 'File not found or has been deleted.'
                ]);
            }
        }

        return view('users.pages.download', compact('files', 'slugs'));
    }

    private function checkFileExists($file)
    {
        $apiKey = env('ONEFICHIER_API_KEY');

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.1fichier.com/v1/download/get_token.cgi', [
                'url' => $file->download_url,
                'inline' => 0,
            ]);

            $tokenData = $response->json();
            return isset($tokenData['status']) && $tokenData['status'] === 'OK';

        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyCaptcha(Request $request)
    {
        $request->validate([
            'captcha' => 'required|string',
            'slug' => 'required|string'
        ]);

        if (strtoupper($request->captcha) !== session('download_captcha')) {
            return response()->json([
                'success' => false,
                'message' => 'Captcha is incorrect.'
            ]);
        }

        $file = UploadedFile::where('slug', $request->slug)->first();

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.'
            ]);
        }

        // Double check if file exists on 1Fichier
        if (!$this->checkFileExists($file)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found or has been deleted.'
            ]);
        }

        $downloadUrl = $this->generateDownloadLink($file);

        if (!$downloadUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate download link.'
            ]);
        }

        session()->forget('download_captcha');

        return response()->json([
            'success' => true,
            'download_url' => $downloadUrl
        ]);
    }

    private function generateDownloadLink($file)
    {
        $apiKey = env('ONEFICHIER_API_KEY');

        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.1fichier.com/v1/download/get_token.cgi', [
                'url' => $file->download_url,
                'inline' => 0,
            ]);

            $tokenData = $response->json();

            if (isset($tokenData['status']) && $tokenData['status'] === 'OK') {
                return $tokenData['url'];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCaptcha()
    {
        if (!session()->has('download_captcha')) {
            session(['download_captcha' => strtoupper(Str::random(5))]);
        }

        $captcha = session('download_captcha');

        $image = imagecreatetruecolor(150, 50);
        $bg = imagecolorallocate($image, 245, 245, 245);
        $textColor = imagecolorallocate($image, 30, 30, 30);
        
        imagefilledrectangle($image, 0, 0, 150, 50, $bg);
        imagestring($image, 5, 50, 15, $captcha, $textColor);

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