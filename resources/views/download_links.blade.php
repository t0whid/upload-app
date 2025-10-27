<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Download Links</title>
</head>
<body>
<h2>✅ Your Download Links</h2>
<ul>
    @foreach($files as $file)
        <li>
            {{ $file->filename }} -
            <a href="{{ $file->download_url }}" target="_blank">Download</a>
        </li>
    @endforeach
</ul>
</body>
</html>
