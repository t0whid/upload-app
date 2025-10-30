@extends('users.layouts.master')
@section('title', 'Download File')
@section('content')
    <style>
        /* Your existing CSS remains the same */
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

        .toast {
            border-radius: 16px;
            box-shadow: 0 15px 35px var(--shadow-color);
            background: var(--toast-bg);
            color: var(--toast-text);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .text-dark {
            color: var(--text-primary) !important;
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

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

        .toast-header {
            background: var(--toast-bg) !important;
            color: var(--toast-text) !important;
            border-bottom: 1px solid var(--border-color) !important;
            border-radius: 16px 16px 0 0 !important;
        }

        #successToast .toast-header {
            background: linear-gradient(135deg, #10b981, #34d399) !important;
            color: white !important;
        }

        #errorToast .toast-header {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: white !important;
        }

        .security-badge {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 20px;
            padding: 8px 16px;
            backdrop-filter: blur(10px);
        }

        @keyframes shine {
            0% { transform: rotate(45deg) translateX(-100%); }
            100% { transform: rotate(45deg) translateX(100%); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .download-card {
            animation: fadeInUp 0.6s ease-out;
        }

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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="download-card p-5">
                    <!-- Header -->
                    <div class="text-center mb-5">
                        <div class="file-icon">
                            <i class="fas fa-file-download"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-3">Download Ready</h2>
                        <p class="text-muted">Your file is securely prepared for download</p>
                    </div>

                    <!-- File Info -->
                    <div class="file-info-card p-4 mb-5">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-2">1Fichier File</h6>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="fas fa-cloud-download-alt me-2"></i>
                                    <span>Secure Download</span>
                                    <span class="mx-3">•</span>
                                    <i class="fas fa-shield-alt me-2"></i>
                                    <span>Protected by hCaptcha</span>
                                </div>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-file text-primary fa-2x"></i>
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
                            <span class="small fw-medium">Secure Download • Protected by hCaptcha • Safe to Use</span>
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
                    <!-- hCaptcha Widget -->
                    <div class="h-captcha" data-sitekey="<?php echo env('HCAPTCHA_SITE_KEY'); ?>"></div>
                    <input type="hidden" id="currentFileSlug" value="{{ $slug }}">

                    <button type="button" class="btn btn-success w-100 mt-3" onclick="verifyAndDownload()" id="verifyBtn">
                        <i class="fa-solid fa-check"></i> Verify & Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & hCaptcha Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- hCaptcha -->
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>

    <script>
        const fileSlug = '{{ $slug }}';

        function showSuccessToast(message) {
            const toastEl = document.getElementById('successToast');
            const toastMessage = document.getElementById('successMessage');
            toastMessage.textContent = message;
            const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 4000 });
            toast.show();
        }

        function showErrorToast(message) {
            const toastEl = document.getElementById('errorToast');
            const toastMessage = document.getElementById('errorMessage');
            toastMessage.textContent = message;
            const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
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

        function showHCaptchaModal() {
            // Reset previous hCaptcha if exists
            if (typeof hcaptcha !== 'undefined') {
                hcaptcha.reset();
            }
            document.getElementById('currentFileSlug').value = fileSlug;
            const modal = new bootstrap.Modal(document.getElementById('hcaptchaModal'));
            modal.show();
        }

        async function verifyAndDownload() {
            const hcaptchaResponse = document.querySelector('[name="h-captcha-response"]');
            
            if (!hcaptchaResponse || !hcaptchaResponse.value) {
                showErrorToast('Please complete the captcha verification');
                return;
            }

            const verifyBtn = document.getElementById('verifyBtn');
            const originalText = verifyBtn.innerHTML;
            
            // Show loading state
            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Verifying...';
            verifyBtn.disabled = true;

            try {
                const response = await fetch('{{ route('file.verify-download') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        'h-captcha-response': hcaptchaResponse.value,
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
                    if (modal) modal.hide();
                    
                    showSuccessToast('Download started successfully!');

                    // Reset hCaptcha
                    if (typeof hcaptcha !== 'undefined') {
                        hcaptcha.reset();
                    }
                } else {
                    showErrorToast(data.message || 'Captcha verification failed');
                    // Reset hCaptcha on error
                    if (typeof hcaptcha !== 'undefined') {
                        hcaptcha.reset();
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

        function startDownloadProcess() {
            showHCaptchaModal();
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Download page loaded with hCaptcha');
        });
    </script>
@endsection