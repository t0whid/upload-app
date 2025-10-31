<?php
// app/Http/Controllers/FileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\TemporaryFile;

class FileController extends Controller
{
    // Show form
    public function showForm()
    {
        return view('users.pages.generate');
    }

    // Handle both single and multiple links
    public function generateLinks(Request $request)
    {
        $request->validate([
            'links' => 'required|string'
        ]);

        $linksText = $request->links;
        $links = $this->extractLinksFromText($linksText);
        
        if (empty($links)) {
            return back()->withErrors(['links' => 'No valid links found.']);
        }

        // Limit to 20 links for performance
        $links = array_slice($links, 0, 20);

        $processedLinks = [];
        $batchId = Str::random(12);

        foreach ($links as $index => $link) {
            $result = $this->processSingleLink($link, $batchId, $index);
            
            if ($result['success']) {
                $processedLinks[] = [
                    'slug' => $result['slug'],
                    'original_url' => $link,
                    'metadata' => $result['metadata']
                ];
            }
        }

        if (empty($processedLinks)) {
            return back()->withErrors(['links' => 'No valid links could be processed.']);
        }

        // Redirect to shareable link
        return redirect()->route('file.links-display', ['batch_id' => $batchId]);
    }

    // Display all links in grid view - SHAREABLE VERSION
    public function linksDisplay($batch_id = null)
    {
        // If batch_id is provided via URL, use it
        $batchId = $batch_id ?? (session('processed_links')['batch_id'] ?? null);
        
        if (!$batchId) {
            return redirect()->route('file.form')->withErrors(['error' => 'No batch ID found.']);
        }

        // Get files from database using batch_id
        $files = TemporaryFile::where('batch_id', $batchId)
                            ->where('expires_at', '>', now())
                            ->orderBy('file_order')
                            ->get();

        if ($files->isEmpty()) {
            return redirect()->route('file.form')->withErrors(['error' => 'Download links expired or invalid.']);
        }

        $processedLinks = [];
        foreach ($files as $file) {
            $processedLinks[] = [
                'slug' => $file->slug,
                'original_url' => $file->original_url,
                'metadata' => json_decode($file->metadata, true)
            ];
        }

        return view('users.pages.links-display', [
            'batch_id' => $batchId,
            'links' => $processedLinks,
            'total_files' => count($processedLinks),
            'shareable_url' => route('file.links-display', ['batch_id' => $batchId]) // Shareable URL
        ]);
    }

    // Extract links from text
    private function extractLinksFromText($text)
    {
        $pattern = '/https?:\/\/[^\s]+/';
        preg_match_all($pattern, $text, $matches);
        
        return array_unique($matches[0] ?? []);
    }

    // Process single link with metadata
    private function processSingleLink($fileLink, $batchId = null, $index = 0)
    {
        try {
            $slug = Str::random(12);

            // Get metadata for the URL
            $metadata = $this->fetchUrlMetadata($fileLink);

            // Store in database
            TemporaryFile::create([
                'slug' => $slug,
                'download_url' => $fileLink,
                'original_url' => $fileLink,
                'expires_at' => now()->addHours(72),
                'batch_id' => $batchId,
                'file_order' => $index,
                'metadata' => json_encode($metadata)
            ]);

            return [
                'success' => true,
                'slug' => $slug,
                'metadata' => $metadata,
                'message' => 'Link processed successfully.'
            ];

        } catch (\Exception $e) {
            \Log::error('Link processing error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Service temporarily unavailable.'
            ];
        }
    }

    // Fetch URL metadata
    private function fetchUrlMetadata($url)
    {
        try {
            $host = parse_url($url, PHP_URL_HOST);
            $siteName = $this->getSiteNameFromHost($host);
            
            return [
                'host' => $host,
                'site_name' => $siteName,
                'favicon' => $this->getFaviconUrl($host),
                'title' => $this->getUrlTitle($url)
            ];
        } catch (\Exception $e) {
            return [
                'host' => parse_url($url, PHP_URL_HOST) ?? 'unknown',
                'site_name' => 'Download Host',
                'favicon' => $this->getDefaultFavicon(),
                'title' => 'Download File'
            ];
        }
    }

    private function getSiteNameFromHost($host)
    {
        if (!$host) return 'Unknown';
        
        $host = str_replace('www.', '', $host);
        $parts = explode('.', $host);
        
        if (count($parts) >= 2) {
            return ucfirst($parts[0]);
        }
        
        return ucfirst($host);
    }

    private function getFaviconUrl($host)
    {
        if (!$host) return $this->getDefaultFavicon();
        return "https://www.google.com/s2/favicons?domain={$host}&sz=32";
    }

    private function getDefaultFavicon()
    {
        return "https://www.google.com/s2/favicons?domain=example.com&sz=32";
    }

    private function getUrlTitle($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $filename = basename($path);
        
        if ($filename && $filename !== '/') {
            return $filename;
        }
        
        return 'Download File';
    }

    // Verify hCaptcha and return original link
    public function verifyAndDownload(Request $request)
    {
        $request->validate([
            'h-captcha-response' => 'required|string',
            'slug' => 'required|string'
        ]);

        // For development, bypass hCaptcha if no secret key
        $secret = env('HCAPTCHA_SECRET_KEY');
        if (!$secret || app()->environment('local')) {
            \Log::info('hCaptcha bypassed');
        } else {
            if (!$this->verifyHCaptcha($request->input('h-captcha-response'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Captcha verification failed. Please try again.'
                ], 422);
            }
        }

        $slug = $request->slug;
        $file = TemporaryFile::where('slug', $slug)
                            ->where('expires_at', '>', now())
                            ->first();

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Download link expired or invalid.'
            ], 404);
        }

        // Return the original link
        return response()->json([
            'success' => true,
            'download_url' => $file->download_url
        ]);
    }

    // Verify hCaptcha and unlock ALL links in batch
    public function verifyAndDownloadAll(Request $request)
    {
        $request->validate([
            'h-captcha-response' => 'required|string',
            'batch_id' => 'required|string'
        ]);

        // For development, bypass hCaptcha if no secret key
        $secret = env('HCAPTCHA_SECRET_KEY');
        if (!$secret || app()->environment('local')) {
            \Log::info('hCaptcha bypassed for batch download');
        } else {
            // Verify hCaptcha in production
            if (!$this->verifyHCaptcha($request->input('h-captcha-response'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Captcha verification failed. Please try again.'
                ], 422);
            }
        }

        $batchId = $request->batch_id;
        $files = TemporaryFile::where('batch_id', $batchId)
                          ->where('expires_at', '>', now())
                          ->get();

        if ($files->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Download links expired or invalid.'
            ], 404);
        }

        // Return all download links
        $downloadLinks = [];
        foreach ($files as $file) {
            $downloadLinks[] = [
                'slug' => $file->slug,
                'download_url' => $file->download_url
            ];
        }

        return response()->json([
            'success' => true,
            'download_links' => $downloadLinks,
            'message' => 'All links unlocked successfully!'
        ]);
    }

    private function verifyHCaptcha($token)
    {
        $secret = env('HCAPTCHA_SECRET_KEY');
        
        if (!$secret) {
            return true;
        }

        try {
            $response = Http::asForm()->timeout(10)->post('https://hcaptcha.com/siteverify', [
                'secret' => $secret,
                'response' => $token
            ]);

            $data = $response->json();
            
            return isset($data['success']) && $data['success'] === true;
        } catch (\Exception $e) {
            \Log::error('hCaptcha verification error: ' . $e->getMessage());
            return false;
        }
    }
}