@extends('users.layouts.master')
@section('title', 'Download Files')

@section('content')
<div class="hosting" style="margin: 40px auto; max-width: 750px;">
    <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">

    <div class="upload-card text-center">
        <h3><i class="fa-solid fa-download text-success"></i> Download Your Files</h3>
        <p class="text-muted">Click download to get your files</p>
        
        <!-- Download Queue Status (hidden initially) -->
        <div id="downloadQueueStatus" style="display:none; background:#fff3cd; border:1px solid #ffeaa7; border-radius:6px; padding:15px; margin-bottom:20px;">
            <h5><i class="fa-solid fa-clock"></i> Download Queue</h5>
            <div id="downloadQueueMessage">Please wait for your turn...</div>
            <div class="progress mt-2" style="height:10px;">
                <div id="downloadQueueProgress" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div>
            </div>
            <small id="downloadQueueDetails" class="text-muted"></small>
        </div>
        
        <ul class="list-group mt-4 text-start">
            @foreach($files as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="file-info">
                        <strong>{{ $file->filename }}</strong>
                        <br>
                        <small class="text-muted">{{ number_format($file->size / 1024 / 1024, 2) }} MB</small>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-success btn-sm download-btn" 
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
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-check-circle me-2"></i>
                <span id="successMessage">Download started successfully!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-exclamation-circle me-2"></i>
                <span id="errorMessage">Something went wrong!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
let downloadQueueCheckInterval = null;
let currentDownloadSlug = '';

// Toast functions
function showSuccessToast(message) {
    const toastEl = document.getElementById('successToast');
    const toastMessage = document.getElementById('successMessage');
    
    toastMessage.textContent = message;
    
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000
    });
    toast.show();
}

function showErrorToast(message) {
    const toastEl = document.getElementById('errorToast');
    const toastMessage = document.getElementById('errorMessage');
    
    toastMessage.textContent = message;
    
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 4000
    });
    toast.show();
}

function copyDownloadLink(slug) {
    const downloadUrl = `{{ url('/download') }}/${slug}`;
    navigator.clipboard.writeText(downloadUrl).then(() => {
        showSuccessToast('Link copied to clipboard!');
    }).catch(() => {
        showErrorToast('Failed to copy link');
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
        showSuccessToast('Captcha refreshed!');
    }).catch(() => {
        showErrorToast('Failed to refresh captcha');
        refreshBtn.innerHTML = originalHtml;
        refreshBtn.disabled = false;
    });
}

function showDownloadQueueStatus(position, current, limit) {
    document.getElementById('downloadQueueStatus').style.display = 'block';
    document.querySelectorAll('.download-btn').forEach(btn => {
        btn.disabled = true;
    });
    
    const progressPercent = Math.min(100, ((position - 1) / limit) * 100);
    document.getElementById('downloadQueueProgress').style.width = progressPercent + '%';
    document.getElementById('downloadQueueMessage').innerHTML = `<strong>Queue Position: ${position}</strong> - Please wait for your turn...`;
    document.getElementById('downloadQueueDetails').innerText = `${current} active users / ${limit} slots available`;
}

function hideDownloadQueueStatus() {
    document.getElementById('downloadQueueStatus').style.display = 'none';
    document.querySelectorAll('.download-btn').forEach(btn => {
        btn.disabled = false;
    });
    
    if (downloadQueueCheckInterval) {
        clearInterval(downloadQueueCheckInterval);
        downloadQueueCheckInterval = null;
    }
}

async function verifyAndDownload() {
    const captcha = document.getElementById('captchaInput').value.trim();
    const slug = document.getElementById('currentFileSlug').value;
    const verifyBtn = document.getElementById('verifyBtn');

    if (!captcha) {
        showErrorToast('Please enter captcha');
        return;
    }

    // Show loading state
    const originalText = verifyBtn.innerHTML;
    verifyBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking queue...';
    verifyBtn.disabled = true;

    try {
        const response = await fetch('{{ route("download.verify-captcha") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({captcha: captcha, slug: slug})
        });

        const data = await response.json();

        if (data.success) {
            // Create invisible iframe for download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = data.download_url;
            document.body.appendChild(iframe);
            
            // Close modal and show success
            bootstrap.Modal.getInstance(document.getElementById('captchaModal')).hide();
            showSuccessToast('Download started successfully!');
            hideDownloadQueueStatus();
        } else {
            if (data.queue_position) {
                showDownloadQueueStatus(data.queue_position, data.current_users, data.limit);
                currentDownloadSlug = slug;
                
                // Auto-retry every 5 seconds
                if (!downloadQueueCheckInterval) {
                    downloadQueueCheckInterval = setInterval(() => {
                        document.getElementById('captchaInput').value = captcha;
                        verifyAndDownload();
                    }, 5000);
                }
            } else {
                showErrorToast(data.message);
                refreshCaptcha();
            }
        }
    } catch (error) {
        showErrorToast('Network error. Please try again.');
    } finally {
        verifyBtn.innerHTML = originalText;
        verifyBtn.disabled = false;
    }
}

// Modal show event
document.getElementById('captchaModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('currentFileSlug').value = button.getAttribute('data-slug');
    document.getElementById('captchaInput').value = '';
    hideDownloadQueueStatus();
    refreshCaptcha();
});

// Enter key support in captcha input
document.getElementById('captchaInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        verifyAndDownload();
    }
});
</script>

<style>
.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
}
.btn-success:hover {
    background: linear-gradient(45deg, #218838, #1e9e8a);
    transform: translateY(-1px);
}
.btn-outline-primary {
    border-color: #007bff;
    color: #007bff;
}
.btn-outline-primary:hover {
    background: #007bff;
    color: white;
}
.toast {
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.progress-bar-striped {
    background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent);
    background-size: 1rem 1rem;
}
</style>
@endsection