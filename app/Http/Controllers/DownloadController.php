<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Services\RateLimitService;

class DownloadController extends Controller
{
    protected $rateLimitService;

    public function __construct(RateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

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
        // Check download rate limit
        $rateLimitCheck = $this->rateLimitService->checkLimit(
            'download',
            session()->getId(),
            $request->ip()
        );

        if (!$rateLimitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => 'Download slot not available. Your download is in queue. Please do not leave this page.',
                'queue_position' => $rateLimitCheck['queue_position'],
                'current_users' => $rateLimitCheck['current'],
                'limit' => $rateLimitCheck['limit']
            ]);
        }

        // Skip captcha verification for single download page
        if ($request->has('captcha') && $request->captcha === 'bypass') {
            // Proceed without captcha verification
            $file = UploadedFile::where('slug', $request->slug)->first();

            if (!$file) {
                $this->rateLimitService->releaseSlot('download', session()->getId());
                return response()->json([
                    'success' => false,
                    'message' => 'File not found.'
                ]);
            }

            // Double check if file exists on 1Fichier
            if (!$this->checkFileExists($file)) {
                $this->rateLimitService->releaseSlot('download', session()->getId());
                return response()->json([
                    'success' => false,
                    'message' => 'File not found or has been deleted.'
                ]);
            }

            $downloadUrl = $this->generateDownloadLink($file);

            if (!$downloadUrl) {
                $this->rateLimitService->releaseSlot('download', session()->getId());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate download link.'
                ]);
            }

            return response()->json([
                'success' => true,
                'download_url' => $downloadUrl
            ]);
        }

        // Original captcha verification code
        $request->validate([
            'h-captcha-response' => 'required|string',
            'slug' => 'required|string'
        ]);

        // Verify hCaptcha
        if (!$this->verifyHCaptcha($request->input('h-captcha-response'))) {
            $this->rateLimitService->releaseSlot('download', session()->getId());
            return response()->json([
                'success' => false,
                'message' => 'Captcha verification failed. Please try again.'
            ]);
        }

        // Continue with file processing after successful captcha verification
        $file = UploadedFile::where('slug', $request->slug)->first();

        if (!$file) {
            $this->rateLimitService->releaseSlot('download', session()->getId());
            return response()->json([
                'success' => false,
                'message' => 'File not found.'
            ]);
        }

        // Double check if file exists on 1Fichier
        if (!$this->checkFileExists($file)) {
            $this->rateLimitService->releaseSlot('download', session()->getId());
            return response()->json([
                'success' => false,
                'message' => 'File not found or has been deleted.'
            ]);
        }

        $downloadUrl = $this->generateDownloadLink($file);

        if (!$downloadUrl) {
            $this->rateLimitService->releaseSlot('download', session()->getId());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate download link.'
            ]);
        }

        return response()->json([
            'success' => true,
            'download_url' => $downloadUrl
        ]);
    }

    private function verifyHCaptcha($token)
    {
        $secret = env('HCAPTCHA_SECRET_KEY');

        try {
            $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
                'secret' => $secret,
                'response' => $token
            ]);

            $data = $response->json();
            return isset($data['success']) && $data['success'] === true;
        } catch (\Exception $e) {
            return false;
        }
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

    public function showSingle($slug)
    {
        $file = UploadedFile::where('slug', $slug)->first();

        if (!$file) {
            return view('users.pages.expired', ['message' => 'File not found.']);
        }

        // Check if file exists on 1Fichier
        if (!$this->checkFileExists($file)) {
            return view('users.pages.expired', [
                'message' => 'File not found or has been deleted.'
            ]);
        }

        return view('users.pages.download_single', compact('file'));
    }
}
