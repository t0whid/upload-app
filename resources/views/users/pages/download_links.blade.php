@extends('users.layouts.master')
@section('title', 'Download Files')

@section('content')
<div class="hosting" style="margin: 40px auto; max-width: 750px;">
    <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">

    <div class="upload-card text-center">
        <h3><i class="fa-solid fa-download text-success"></i> Your Download Links</h3>
        <p>Click the download button to start downloading your files.</p>

        <ul class="list-group mt-4 text-start">
            @foreach($downloadLinks as $link)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="file-info">
                        <i class="fa-regular fa-file-zipper text-primary"></i>
                        <strong>{{ $link['filename'] }}</strong>
                        <br>
                        <small class="text-muted">{{ number_format($link['size'] / 1024 / 1024, 2) }} MB</small>
                    </div>

                    <div class="btn-group">
                        <!-- Download Button -->
                        <a href="{{ $link['download_url'] }}" 
                           class="btn btn-success" 
                           target="_blank">
                            <i class="fa-solid fa-download"></i> Download
                        </a>

                        <!-- Copy Button -->
                        <button class="btn btn-outline-primary" 
                                onclick="copyLink('{{ $link['download_url'] }}', this)">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-4">
            <p class="text-muted">
                <small>Download links are valid for 5 minutes</small>
            </p>
        </div>
    </div>
</div>

<script>
function copyLink(link, btn) {
    navigator.clipboard.writeText(link).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
        setTimeout(() => btn.innerHTML = '<i class="fa-solid fa-copy"></i>', 1500);
    }).catch(() => alert("Failed to copy"));
}
</script>
@endsection