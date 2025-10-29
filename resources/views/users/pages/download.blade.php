@extends('users.layouts.master')
@section('title', 'Download Files')

@section('content')
<div class="hosting" style="margin: 40px auto; max-width: 750px;">
    <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">

    <div class="upload-card text-center">
        <h3><i class="fa-solid fa-download text-success"></i> Download Your Files</h3>
        <p class="text-muted">Click download to get your files</p>
        
        <ul class="list-group mt-4 text-start">
            @foreach($files as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="file-info">
                        <strong>{{ $file->filename }}</strong>
                        <br>
                        <small class="text-muted">{{ number_format($file->size / 1024 / 1024, 2) }} MB</small>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-success btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#captchaModal"
                                data-slug="{{ $file->slug }}">
                            <i class="fa-solid fa-download"></i> Download
                        </button>

                        <button class="btn btn-outline-primary btn-sm" 
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
                     class="img-fluid border rounded">
                <button type="button" onclick="refreshCaptcha()" class="btn btn-sm btn-outline-secondary mt-2">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>

                <input type="text" id="captchaInput" class="form-control my-3" placeholder="Enter captcha here">
                <input type="hidden" id="currentFileSlug">
                
                <button type="button" class="btn btn-success w-100" onclick="verifyAndDownload()" id="verifyBtn">
                    <i class="fa-solid fa-check"></i> Verify & Download
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert">
        <div class="toast-header">
            <i class="fa-solid fa-check-circle text-success me-2"></i>
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Download started successfully!
        </div>
    </div>
</div>

<script>
// Toast function
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('liveToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastHeader = toastEl.querySelector('.toast-header i');
    
    // Set message
    toastMessage.textContent = message;
    
    // Set icon and color based on type
    if (type === 'error') {
        toastHeader.className = 'fa-solid fa-exclamation-circle text-danger me-2';
        toastEl.querySelector('.toast-header strong').textContent = 'Error';
    } else {
        toastHeader.className = 'fa-solid fa-check-circle text-success me-2';
        toastEl.querySelector('.toast-header strong').textContent = 'Success';
    }
    
    // Show toast
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

function copyDownloadLink(slug) {
    const downloadUrl = `{{ url('/download') }}/${slug}`;
    navigator.clipboard.writeText(downloadUrl).then(() => {
        showToast('Link copied to clipboard!');
    }).catch(() => {
        showToast('Failed to copy link', 'error');
    });
}

function refreshCaptcha() {
    const refreshBtn = document.querySelector('#captchaModal .btn-outline-secondary');
    const originalHtml = refreshBtn.innerHTML;
    
    refreshBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing';
    refreshBtn.disabled = true;

    fetch('{{ route("download.refresh-captcha") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    }).then(() => {
        document.getElementById('captchaImage').src = '{{ route("download.captcha") }}?t=' + Date.now();
        document.getElementById('captchaInput').value = '';
        refreshBtn.innerHTML = originalHtml;
        refreshBtn.disabled = false;
    });
}

function verifyAndDownload() {
    const captcha = document.getElementById('captchaInput').value.trim();
    const slug = document.getElementById('currentFileSlug').value;
    const verifyBtn = document.getElementById('verifyBtn');

    if (!captcha) {
        showToast('Please enter captcha', 'error');
        return;
    }

    // Show loading state
    const originalText = verifyBtn.innerHTML;
    verifyBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';
    verifyBtn.disabled = true;

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
            
            // Close modal and show success
            bootstrap.Modal.getInstance(document.getElementById('captchaModal')).hide();
            showToast('Download started successfully!');
        } else {
            showToast(data.message, 'error');
            refreshCaptcha();
        }
    })
    .catch(error => {
        showToast('Network error. Please try again.', 'error');
    })
    .finally(() => {
        verifyBtn.innerHTML = originalText;
        verifyBtn.disabled = false;
    });
}

// Modal show event
document.getElementById('captchaModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('currentFileSlug').value = button.getAttribute('data-slug');
    document.getElementById('captchaInput').value = '';
    refreshCaptcha();
});
</script>

<style>
.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
}
.btn-success:hover {
    background: linear-gradient(45deg, #218838, #1e9e8a);
}
.toast {
    background: white;
    border-left: 4px solid #28a745;
}
</style>
@endsection