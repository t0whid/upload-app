<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Download {{ $file->filename }}</title>
<style>
body { font-family: sans-serif; margin: 40px; }
a.button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
a.button:hover { background: #45a049; }
</style>
</head>
<body>
<h2>📥 Download File</h2>
<p><strong>Filename:</strong> {{ $file->filename }}</p>
<p><strong>Size:</strong> {{ number_format($file->size / 1024, 2) }} KB</p>
<p><strong>Whirlpool Checksum:</strong> {{ $file->whirlpool ?? 'N/A' }}</p>
<a href="{{ $file->download_url }}" class="button" target="_blank">Download Now</a>
@if($file->remove_url)
<p>Remove Link: <a href="{{ $file->remove_url }}" target="_blank">{{ $file->remove_url }}</a></p>
@endif
</body>
</html>
