@extends('users.layouts.master')
@section('title', 'Download Files')
@section('content')
<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --dark: #1f2937;
        --light: #f8fafc;
    }

    .light-theme {
        --bg-primary: #ffffff;
        --bg-secondary: #f8f9fa;
        --text-primary: #000000;
        --text-secondary: #6c757d;
        --border-color: #dee2e6;
        --card-bg: rgba(255, 255, 255, 0.95);
        --file-info-bg: #f8f9fa;
        --toast-bg: #ffffff;
        --toast-text: #000000;
        --shadow-color: rgba(0, 0, 0, 0.1);
    }

    .dark-theme {
        --bg-primary: #151820;
        --bg-secondary: #1c1f2e;
        --text-primary: #ffffff;
        --text-secondary: #adb5bd;
        --border-color: #343a40;
        --card-bg: rgba(21, 24, 32, 0.95);
        --file-info-bg: #1c1f2e;
        --toast-bg: #1c1f2e;
        --toast-text: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.3);
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
        transition: all 0.3s ease;
    }

    .download-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 0 25px 50px var(--shadow-color);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .download-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .file-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        position: relative;
        overflow: hidden;
    }

    .file-icon::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(45deg);
        animation: shine 3s infinite;
    }

    .file-icon i {
        font-size: 2.5rem;
        color: white;
        z-index: 2;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        border-radius: 14px;
        padding: 16px 35px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
    }

    .btn-primary:active {
        transform: translateY(-1px);
    }

    .file-info-card {
        background: var(--file-info-bg);
        border-radius: 16px;
        border-left: 5px solid var(--primary);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .file-info-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(79, 70, 229, 0.05));
        border-radius: 0 16px 0 80px;
    }

    .progress-bar {
        background: linear-gradient(135deg, var(--success), #34d399);
    }

    .toast {
        border-radius: 16px;
        box-shadow: 0 15px 35px var(--shadow-color);
        background: var(--toast-bg);
        color: var(--toast-text);
        transition: all 0.3s ease;
        border: 1px solid var(--border-color);
    }

    /* Text colors that change with theme */
    .text-dark {
        color: var(--text-primary) !important;
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    /* Alert styling for dark theme */
    .dark-theme .alert-warning {
        background: rgba(255, 193, 7, 0.1) !important;
        border: 1px solid rgba(255, 193, 7, 0.3) !important;
        color: var(--text-primary) !important;
        backdrop-filter: blur(10px);
    }

    /* Button styling */
    .btn-outline-secondary {
        border: 2px solid var(--border-color);
        color: var(--text-primary);
        background: transparent;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background: var(--bg-secondary);
        border-color: var(--border-color);
        color: var(--text-primary);
        transform: translateY(-2px);
    }

    .btn-light {
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        background: var(--bg-primary);
        color: var(--text-primary);
        border-color: var(--border-color);
        transform: translateY(-2px);
    }

    /* Toast header theming */
    .toast-header {
        background: var(--toast-bg) !important;
        color: var(--toast-text) !important;
        border-bottom: 1px solid var(--border-color) !important;
        border-radius: 16px 16px 0 0 !important;
    }

    /* Success and error toast theming */
    #successToast .toast-header {
        background: linear-gradient(135deg, #10b981, #34d399) !important;
        color: white !important;
    }

    #errorToast .toast-header {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: white !important;
    }

    /* Security badge */
    .security-badge {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 20px;
        padding: 8px 16px;
        backdrop-filter: blur(10px);
    }

    /* Animation */
    @keyframes shine {
        0% {
            transform: rotate(45deg) translateX(-100%);
        }

        100% {
            transform: rotate(45deg) translateX(100%);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .download-card {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Loading spinner */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* hCaptcha Modal Styling */
    .h-captcha {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .modal-content {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .modal-header {
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-secondary);
    }

    .modal-title {
        color: var(--text-primary);
        font-weight: 600;
    }
</style>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="download-card p-5">
                    <!-- Header -->
                    <div class="text-center mb-5">
                        <div class="file-icon">
                            <i class="fas fa-file-download"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-3" style="font-size: 2rem;">Download Ready</h2>
                        <p class="text-muted" style="font-size: 1.1rem;">Your file is securely prepared for download</p>
                    </div>

                    <!-- File Info -->
                    <div class="file-info-card p-4 mb-5">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-2" style="font-size: 1.1rem;">{{ $file->filename }}</h6>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="fas fa-hdd me-2"></i>
                                    <span>{{ number_format($file->size / 1024 / 1024, 2) }} MB</span>
                                    <span class="mx-3">•</span>
                                    <i class="fas fa-calendar me-2"></i>
                                    <span>{{ $file->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-file text-primary fa-2x"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Download Queue Status -->
                    <div id="downloadQueueStatus" style="display:none;" class="alert alert-warning border-0 rounded-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-lg me-3"></i>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-2">Download Queue</h6>
                                <div id="downloadQueueMessage" class="small mb-2">Please wait for your turn...</div>
                                <div class="progress mt-2" style="height: 8px; border-radius: 10px;">
                                    <div id="downloadQueueProgress"
                                        class="progress-bar progress-bar-striped progress-bar-animated"
                                        style="width:0%; border-radius: 10px;"></div>
                                </div>
                                <small id="downloadQueueDetails" class="text-muted mt-2 d-block"></small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-3">
                        <button class="btn btn-primary btn-lg" onclick="startDownloadProcess()" id="downloadBtn">
                            <i class="fas fa-download me-2"></i>Download Now
                        </button>

                        <button class="btn btn-outline-secondary" onclick="copyDownloadLink()">
                            <i class="fas fa-link me-2"></i>Copy Download Link
                        </button>

                        <button class="btn btn-light" onclick="window.close()">
                            <i class="fas fa-times me-2"></i>Close Window
                        </button>
                    </div>

                    <!-- Security Info -->
                    <div class="mt-5 text-center">
                        <div class="security-badge d-inline-flex align-items-center px-3 py-2">
                            <i class="fas fa-shield-alt me-2 text-success"></i>
                            <span class="small fw-medium">Secure Download • Virus Scanned • Safe to Use</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert">
            <div class="toast-header border-0">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <span id="successMessage">Download started successfully!</span>
            </div>
        </div>

        <div id="errorToast" class="toast" role="alert">
            <div class="toast-header border-0">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <span id="errorMessage">Something went wrong!</span>
            </div>
        </div>
    </div>

    <!-- hCaptcha Modal -->
    <div class="modal fade" id="hcaptchaModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify You're Human</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="h-captcha" data-sitekey="{{ env('HCAPTCHA_SITE_KEY') }}"></div>
                    <input type="hidden" id="currentFileSlug">

                    <button type="button" class="btn btn-success w-100 mt-3" onclick="verifyAndDownload()" id="verifyBtn">
                        <i class="fa-solid fa-check"></i> Verify & Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- hCaptcha Script -->
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let downloadQueueCheckInterval = null;
        const fileSlug = '{{ $file->slug }}';

        // Toast functions
        function showSuccessToast(message) {
            const toastEl = document.getElementById('successToast');
            const toastMessage = document.getElementById('successMessage');

            toastMessage.textContent = message;

            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 4000
            });
            toast.show();
        }

        function showErrorToast(message) {
            const toastEl = document.getElementById('errorToast');
            const toastMessage = document.getElementById('errorMessage');

            toastMessage.textContent = message;

            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        }

        function copyDownloadLink() {
            const downloadUrl = window.location.href;
            navigator.clipboard.writeText(downloadUrl).then(() => {
                showSuccessToast('Download link copied to clipboard!');
            }).catch(() => {
                showErrorToast('Failed to copy link');
            });
        }

        function showDownloadQueueStatus(position, current, limit) {
            document.getElementById('downloadQueueStatus').style.display = 'block';
            document.getElementById('downloadBtn').disabled = true;

            const progressPercent = Math.min(100, ((position - 1) / limit) * 100);
            document.getElementById('downloadQueueProgress').style.width = progressPercent + '%';
            document.getElementById('downloadQueueMessage').innerHTML =
                `<strong>Queue Position: ${position}</strong> - Please wait for your turn...`;
            document.getElementById('downloadQueueDetails').innerText =
                `${current} active users / ${limit} slots available`;
        }

        function hideDownloadQueueStatus() {
            document.getElementById('downloadQueueStatus').style.display = 'none';
            document.getElementById('downloadBtn').disabled = false;

            if (downloadQueueCheckInterval) {
                clearInterval(downloadQueueCheckInterval);
                downloadQueueCheckInterval = null;
            }
        }

        function showHCaptchaModal() {
            document.getElementById('currentFileSlug').value = fileSlug;
            const modal = new bootstrap.Modal(document.getElementById('hcaptchaModal'));
            modal.show();
        }

        async function verifyAndDownload() {
            const hcaptchaResponse = document.querySelector('[name="h-captcha-response"]').value;
            const verifyBtn = document.getElementById('verifyBtn');

            if (!hcaptchaResponse) {
                showErrorToast('Please complete the captcha verification');
                return;
            }

            // Show loading state
            const originalText = verifyBtn.innerHTML;
            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Verifying...';
            verifyBtn.disabled = true;

            try {
                const response = await fetch('{{ route('download.verify-captcha') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        'h-captcha-response': hcaptchaResponse,
                        slug: fileSlug
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Create invisible iframe for download
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = data.download_url;
                    document.body.appendChild(iframe);

                    // Close modal and show success
                    const modal = bootstrap.Modal.getInstance(document.getElementById('hcaptchaModal'));
                    if (modal) {
                        modal.hide();
                    }
                    showSuccessToast('Download started successfully!');
                    hideDownloadQueueStatus();

                    // Reset hCaptcha
                    if (typeof hcaptcha !== 'undefined') {
                        hcaptcha.reset();
                    }
                } else {
                    if (data.queue_position) {
                        showDownloadQueueStatus(data.queue_position, data.current_users, data.limit);

                        // Auto-retry every 5 seconds
                        if (!downloadQueueCheckInterval) {
                            downloadQueueCheckInterval = setInterval(() => {
                                verifyAndDownload();
                            }, 5000);
                        }
                    } else {
                        showErrorToast(data.message || 'Captcha verification failed');
                        // Reset hCaptcha on error
                        if (typeof hcaptcha !== 'undefined') {
                            hcaptcha.reset();
                        }
                    }
                }
            } catch (error) {
                console.error('Captcha verification error:', error);
                showErrorToast('Network error. Please try again.');
                if (typeof hcaptcha !== 'undefined') {
                    hcaptcha.reset();
                }
            } finally {
                verifyBtn.innerHTML = originalText;
                verifyBtn.disabled = false;
            }
        }

        // Update startDownloadProcess function to show captcha modal
        function startDownloadProcess() {
            showHCaptchaModal();
        }

        // Enter key support for accessibility
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                startDownloadProcess();
            }
        });

        // Update toast close button color based on theme
        function updateToastCloseButtons() {
            const isDarkTheme = document.documentElement.classList.contains('dark-theme');
            const closeButtons = document.querySelectorAll('.btn-close');

            closeButtons.forEach(btn => {
                if (isDarkTheme) {
                    btn.classList.add('btn-close-white');
                } else {
                    btn.classList.remove('btn-close-white');
                }
            });
        }

        // Add hover effects to cards
        function addHoverEffects() {
            const downloadCard = document.querySelector('.download-card');
            const fileInfoCard = document.querySelector('.file-info-card');

            if (downloadCard) {
                downloadCard.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 35px 60px var(--shadow-color)';
                });

                downloadCard.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 25px 50px var(--shadow-color)';
                });
            }
        }

        // Initialize and watch for theme changes
        document.addEventListener('DOMContentLoaded', function() {
            updateToastCloseButtons();
            addHoverEffects();

            // Watch for theme changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        updateToastCloseButtons();
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true
            });
        });
    </script>
@endsection