@extends('users.layouts.master')
@section('title', 'Download Files')

@section('content')
<div class="hosting" style="margin: 40px auto; max-width: 750px;">
    <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">

    <div class="upload-card text-center">
        <h3><i class="fa-solid fa-download text-success"></i> Download Your Files</h3>
        
        <ul class="list-group mt-4 text-start">
            @foreach($files as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="file-info">
                        <strong>{{ $file->filename }}</strong>
                        <br>
                        <small class="text-muted">{{ number_format($file->size / 1024 / 1024, 2) }} MB</small>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-success" 
                                data-bs-toggle="modal" 
                                data-bs-target="#captchaModal"
                                data-slug="{{ $file->slug }}">
                            <i class="fa-solid fa-download"></i> Download
                        </button>

                        <button class="btn btn-outline-primary" 
                                onclick="copyDownloadLink('{{ $file->slug }}')">
                            <i class="fa-solid fa-copy"></i> Copy
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- Captcha Modal -->
<div class="modal fade" id="captchaModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify Captcha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ route('download.captcha') }}" alt="Captcha" id="captchaImage" 
                     style="border:1px solid #ccc; width: 100%;">
                <button type="button" onclick="refreshCaptcha()" class="btn btn-sm btn-outline-secondary mt-2">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>

                <input type="text" id="captchaInput" class="form-control my-3" placeholder="Enter captcha">
                <input type="hidden" id="currentFileSlug">
                
                <div id="captchaMessage" class="text-danger small mb-2"></div>
                
                <button type="button" class="btn btn-success w-100" onclick="verifyAndDownload()">
                    Verify & Download
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function copyDownloadLink(slug) {
    const downloadUrl = `{{ url('/download') }}/${slug}`;
    navigator.clipboard.writeText(downloadUrl);
    alert('Link copied!');
}

function refreshCaptcha() {
    fetch('{{ route("download.refresh-captcha") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    }).then(() => {
        document.getElementById('captchaImage').src = '{{ route("download.captcha") }}?t=' + Date.now();
        document.getElementById('captchaInput').value = '';
        document.getElementById('captchaMessage').textContent = '';
    });
}

function verifyAndDownload() {
    const captcha = document.getElementById('captchaInput').value;
    const slug = document.getElementById('currentFileSlug').value;
    const messageDiv = document.getElementById('captchaMessage');

    if (!captcha) {
        messageDiv.textContent = 'Please enter captcha';
        return;
    }

    fetch('{{ route("download.verify-captcha") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({captcha: captcha, slug: slug})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Create invisible iframe for download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = data.download_url;
            document.body.appendChild(iframe);
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('captchaModal')).hide();
            alert('Download started!');
        } else {
            messageDiv.textContent = data.message;
            refreshCaptcha();
        }
    });
}

// Modal show event
document.getElementById('captchaModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('currentFileSlug').value = button.getAttribute('data-slug');
    document.getElementById('captchaInput').value = '';
    document.getElementById('captchaMessage').textContent = '';
    refreshCaptcha();
});
</script>
@endsection