@extends('users.layouts.master')
@section('title', 'Download Files')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="hosting" style="margin: 40px auto; max-width: 750px;">
    <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">

    <div class="upload-card text-center">
        <h3><i class="fa-solid fa-download text-success"></i> Your Download Links</h3>
        <p>You can now download or share your uploaded files securely.</p>

        <ul class="list-group mt-4 text-start">
            @foreach($files as $file)
                @php
                    $shareText = urlencode("Download this file: {$file->filename}");
                    $shareUrl = urlencode($file->download_url);
                @endphp
                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <div class="file-info">
                        <i class="fa-regular fa-file-zipper text-primary"></i>
                        <strong>{{ $file->filename }}</strong>
                    </div>

                    <div class="btn-group mt-2 mt-sm-0">
                        <!-- Download -->
                        <a href="{{ $file->download_url }}" target="_blank" class="btn btn-sm btn-success">
                            <i class="fa-solid fa-download"></i>
                        </a>

                        <!-- Copy -->
                        <button class="btn btn-sm btn-outline-primary" onclick="copyLink('{{ $file->download_url }}', this)">
                            <i class="fa-solid fa-copy"></i>
                        </button>

                        <!-- Share -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-share-nodes"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" target="_blank"
                                       href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}">
                                        <i class="fa-brands fa-facebook text-primary"></i> Facebook
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" target="_blank"
                                       href="https://api.whatsapp.com/send?text={{ $shareText }}%20{{ $shareUrl }}">
                                        <i class="fa-brands fa-whatsapp text-success"></i> WhatsApp
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" target="_blank"
                                       href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}">
                                        <i class="fa-brands fa-telegram text-info"></i> Telegram
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" target="_blank"
                                       href="https://twitter.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}">
                                        <i class="fa-brands fa-x-twitter text-dark"></i> X (Twitter)
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
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
