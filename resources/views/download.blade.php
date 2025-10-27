<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Download Files</title>
<style>
body { font-family: sans-serif; margin: 40px; }
a.button { display: inline-block; margin: 10px 0; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
a.button:hover { background: #45a049; }
</style>
</head>
<body>
<h2>📥 Download {{ $files->count() == 1 ? 'File' : 'Files' }}</h2>

@foreach($files as $file)
    <p><strong>{{ $file->filename }}</strong> ({{ number_format($file->size / 1024 / 1024, 2) }} MB)</p>
    <a href="{{ $file->download_url }}" class="button" target="_blank">Download</a>
    @if($file->remove_url)
        <p>Remove Link: <a href="{{ $file->remove_url }}" target="_blank">{{ $file->remove_url }}</a></p>
    @endif
    <hr>
@endforeach
</body>
</html>
