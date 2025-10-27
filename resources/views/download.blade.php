<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Download Files</title>
<style>
body { font-family: sans-serif; margin: 40px; }
.captcha-box { margin-top: 20px; }
</style>
</head>
<body>
<h2>🔒 Verify Captcha to Reveal Download Links</h2>

<form action="{{ route('download.verify') }}" method="POST">
    @csrf
    <input type="hidden" name="slugs" value="{{ $slugs }}">
    <div class="captcha-box">
        <img src="{{ route('download.captcha') }}" alt="Captcha Image">
        <br>
        <input type="text" name="captcha" placeholder="Enter Captcha">
        @error('captcha') <div style="color:red">{{ $message }}</div> @enderror
        <br>
        <a href="#" onclick="this.previousElementSibling.src='{{ route('download.captcha') }}?'+Math.random(); return false;">Refresh Captcha</a>
    </div>
    <button type="submit">Verify</button>
</form>
</body>
</html>
