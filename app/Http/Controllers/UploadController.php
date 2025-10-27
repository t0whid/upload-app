<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Events\UploadProgress;
use App\Models\UploadedFile;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:512000' // ~500MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();

        // Save temp locally
        $localPath = $file->storeAs('temp_uploads', uniqid() . '_' . $originalName);
        $absolutePath = storage_path('app/' . $localPath);

        $apiKey = env('ONEFICHIER_API_KEY');

        // Step 1: Get 1Fichier upload server
        $ch = curl_init('https://api.1fichier.com/v1/upload/get_upload_server.cgi');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['pretty' => 1]));
        $serverResp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || !$serverResp) {
            Storage::delete($localPath);
            return response()->json(['error' => 'Could not get 1fichier upload server'], 500);
        }

        $serverJson = json_decode($serverResp, true);
        $uploadId = $serverJson['id'] ?? uniqid();
        $uploadUrl = 'https://' . ($serverJson['url'] ?? 'upload.1fichier.com') . '/upload.cgi?id=' . $uploadId;

        // Step 2: Upload file to 1Fichier
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $apiKey]);

        $cfile = new \CURLFile($absolutePath, mime_content_type($absolutePath), $originalName);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file[]' => $cfile]);

        // Enable progress callback
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $dlsize, $dloaded, $uls, $ul) use ($size) {
            static $lastTime = null;
            static $lastUploaded = 0;

            $now = microtime(true);
            if (!$lastTime) $lastTime = $now;
            $elapsed = $now - $lastTime;
            if ($elapsed <= 0) return 0;

            $bytesUploaded = $ul ?: $lastUploaded;
            $speedBps = ($bytesUploaded - $lastUploaded) / $elapsed;
            $lastTime = $now;
            $lastUploaded = $bytesUploaded;

            $percent = $size > 0 ? round(($bytesUploaded / $size) * 100, 2) : 0;
            $speedMB = round($speedBps / 1024 / 1024, 2);

            broadcast(new UploadProgress('laravel->1fichier', $percent, (int)$bytesUploaded, $speedMB));
            return 0;
        });

        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);
        Storage::delete($localPath);

        if ($curlErr) {
            broadcast(new UploadProgress('laravel->1fichier', 0, 0, 0, 'Upload failed'));
            return response()->json(['error' => $curlErr], 500);
        }

        broadcast(new UploadProgress('laravel->1fichier', 100, $size, 0, 'Upload finished'));

        // Step 3: Get final download links
        $endUrl = "https://" . ($serverJson['url'] ?? 'upload.1fichier.com') . "/end.pl?xid={$uploadId}";
        $ch = curl_init($endUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $apiKey, 'JSON: 1']);
        $endResp = curl_exec($ch);
        curl_close($ch);

        $savedLinks = [];
        if ($endResp) {
            $endJson = json_decode($endResp, true);
            if (!empty($endJson['links'])) {
                foreach ($endJson['links'] as $link) {
                    $slug = Str::random(10);
                    $savedLinks[] = UploadedFile::create([
                        'slug' => $slug,
                        'filename' => $link['filename'],
                        'download_url' => $link['download'],
                        'remove_url' => $link['remove'] ?? null,
                        'size' => $link['size'] ?? 0,
                        'whirlpool' => $link['whirlpool'] ?? null,
                    ]);
                }
            }
        }

        $firstSlug = $savedLinks[0]->slug ?? null;

        return response()->json([
            'status' => 'ok',
            'upload_id' => $uploadId,
            'first_slug' => $firstSlug,
        ]);
    }
}
