<?php
// app/Http/Controllers/FileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\TemporaryFile;

class FileController extends Controller
{
    // Show form
    public function showForm()
    {
        return view('users.pages.generate');
    }

    // Handle single link submission
    public function generate(Request $request)
    {
        $request->validate([
            'link' => 'required|url'
        ]);

        $fileLink = $request->link;
        
        // Process single link (NO API CALL)
        $result = $this->processSingleLink($fileLink);
        
        if (!$result['success']) {
            return back()->withErrors(['link' => $result['message']]);
        }

        return redirect()->route('file.download', ['slug' => $result['slug']]);
    }

    // Handle bulk links submission
    public function generateBulk(Request $request)
    {
        $request->validate([
            'bulk_links' => 'required|string'
        ]);

        $linksText = $request->bulk_links;
        $links = $this->extractLinksFromText($linksText);
        
        if (empty($links)) {
            return back()->withErrors(['bulk_links' => 'No valid links found.']);
        }

        // Limit to 20 links per batch for performance
        $links = array_slice($links, 0, 20);

        $successfulSlugs = [];
        $failedLinks = [];

        foreach ($links as $link) {
            $result = $this->processSingleLink($link);
            
            if ($result['success']) {
                $successfulSlugs[] = $result['slug'];
            } else {
                $failedLinks[] = [
                    'url' => $link,
                    'error' => $result['message']
                ];
            }
        }

        // Store successful slugs in session for JavaScript to open tabs
        if (!empty($successfulSlugs)) {
            session()->flash('bulk_slugs', $successfulSlugs);
        }

        if (!empty($failedLinks)) {
            session()->flash('bulk_failed_links', $failedLinks);
        }

        // Redirect back to form with success message
        return redirect()->route('file.form')->with('success', count($successfulSlugs) . ' links processed successfully!');
    }

    // Extract links from text
    private function extractLinksFromText($text)
    {
        $pattern = '/https?:\/\/[^\s]+/';
        preg_match_all($pattern, $text, $matches);
        
        return array_unique($matches[0] ?? []);
    }

    // Process single link - NO API CALL
    private function processSingleLink($fileLink, $batchId = null)
    {
        try {
            $slug = Str::random(12);

            // Store in database - store the original link directly
            TemporaryFile::create([
                'slug' => $slug,
                'download_url' => $fileLink, // Store original link
                'original_url' => $fileLink,
                'expires_at' => now()->addHours(72),
                'batch_id' => $batchId
            ]);

            return [
                'success' => true,
                'slug' => $slug,
                'message' => 'Link processed successfully.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Service temporarily unavailable.'
            ];
        }
    }

    // Single download page
    public function download($slug)
    {
        $file = TemporaryFile::where('slug', $slug)
                            ->where('expires_at', '>', now())
                            ->first();

        if (!$file) {
            return redirect()->route('file.form')->withErrors(['link' => 'Download link expired or invalid.']);
        }

        return view('users.pages.single', [
            'slug' => $slug, 
            'file' => $file
        ]);
    }

    // Verify hCaptcha and redirect to original link
    public function verifyAndDownload(Request $request)
    {
        $request->validate([
            'h-captcha-response' => 'required|string',
            'slug' => 'required|string'
        ]);

        // For development, bypass hCaptcha
        if (app()->environment('local')) {
            \Log::info('hCaptcha bypassed in local development');
        } else {
            // Verify hCaptcha in production
            if (!$this->verifyHCaptcha($request->input('h-captcha-response'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Captcha verification failed. Please try again.'
                ]);
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
            ]);
        }

        // Return the original link for redirection
        return response()->json([
            'success' => true,
            'download_url' => $file->download_url
        ]);
    }

    private function verifyHCaptcha($token)
    {
        $secret = env('HCAPTCHA_SECRET_KEY');
        
        if (!$secret) {
            return app()->environment('local') ? true : false;
        }

        try {
            $response = Http::asForm()->timeout(10)->post('https://hcaptcha.com/siteverify', [
                'secret' => $secret,
                'response' => $token
            ]);

            $data = $response->json();
            
            return isset($data['success']) && $data['success'] === true;
        } catch (\Exception $e) {
            return false;
        }
    }
}