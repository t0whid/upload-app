{{-- resources/views/users/pages/single.blade.php --}}
@extends('users.layouts.master')
@section('title', 'Download File')
@section('content')
<style>
    /* Your existing CSS remains exactly the same as generate page */
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
        --input-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.1);
    }

    .dark-theme {
        --bg-primary: #151820;
        --bg-secondary: #1c1f2e;
        --text-primary: #ffffff;
        --text-secondary: #adb5bd;
        --border-color: #343a40;
        --card-bg: rgba(21, 24, 32, 0.95);
        --input-bg: #1c1f2e;
        --shadow-color: rgba(0, 0, 0, 0.3);
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
        transition: all 0.3s ease;
        margin: 0;
        padding: 20px;
        box-sizing: border-box;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
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
        margin: 20px 0;
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
        width: 80px;
        height: 80px;
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
        font-size: 2rem;
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
        color: white;
        cursor: pointer;
        width: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
    }

    .btn-primary:active {
        transform: translateY(-1px);
    }

    .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        border-radius: 14px;
        padding: 16px 35px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        color: white;
        cursor: pointer;
        width: 100%;
    }

    .btn-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
    }

    .feature-card {
        background: var(--bg-secondary);
        border-radius: 16px;
        border-left: 4px solid var(--primary);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        padding: 20px;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(79, 70, 229, 0.05));
        border-radius: 0 16px 0 60px;
    }

    .security-badge {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 20px;
        padding: 12px 20px;
        backdrop-filter: blur(10px);
        display: inline-flex;
        align-items: center;
        margin-top: 20px;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: var(--text-primary);
        border-radius: 16px;
    }

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

    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
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

    .alert {
        border-radius: 16px;
        border: none;
        backdrop-filter: blur(10px);
        padding: 20px;
        margin-bottom: 20px;
    }

    .text-dark {
        color: var(--text-primary) !important;
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    .min-vh-100 {
        min-height: 100vh;
    }

    .py-5 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }

    .mb-4 {
        margin-bottom: 1.5rem !important;
    }

    .mb-5 {
        margin-bottom: 3rem !important;
    }

    .mt-5 {
        margin-top: 3rem !important;
    }

    .mb-3 {
        margin-bottom: 1rem !important;
    }

    .h-captcha {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .captcha-container {
        background: var(--bg-secondary);
        border-radius: 16px;
        padding: 25px;
        margin: 20px 0;
        border: 2px dashed var(--border-color);
    }

    .file-info {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 12px;
        padding: 15px;
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .file-info i {
        color: #10b981;
        font-size: 1.5rem;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .download-card {
            padding: 30px 20px !important;
            margin: 10px 0;
        }
        
        body {
            padding: 10px;
        }
    }
</style>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-5">
        <div class="col-md-8 col-lg-6">
            <div class="download-card p-5">
                <!-- Header -->
                <div class="text-center mb-5">
                    <div class="file-icon">
                        <i class="fas fa-file-download"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-3" style="font-size: 2rem;">Download Ready</h2>
                    <p class="text-muted" style="font-size: 1.1rem;">Complete captcha to unlock your download</p>
                </div>

                <!-- File Info -->
                <div class="file-info">
                    <i class="fas fa-shield-check"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Secure Download</h6>
                        <p class="text-muted mb-0 small">Your file is protected and ready for download</p>
                    </div>
                </div>

                <!-- Captcha Section -->
                <div class="captcha-container">
                    <h5 class="fw-bold text-dark mb-3 text-center">
                        <i class="fas fa-robot me-2 text-primary"></i>
                        Verify You're Human
                    </h5>
                    <p class="text-muted text-center mb-4">Complete the captcha below to unlock the download</p>
                    
                    <!-- hCaptcha Widget -->
                    <div class="h-captcha" data-sitekey="<?php echo env('HCAPTCHA_SITE_KEY'); ?>"></div>
                </div>

                <!-- Download Button -->
                <div class="d-grid">
                    <button class="btn btn-success btn-lg" onclick="startDownloadProcess()" id="downloadBtn">
                        <i class="fas fa-download me-2"></i>
                        <span id="btnText">UNLOCK DOWNLOAD</span>
                        <div class="loading-spinner ms-2" id="btnSpinner"></div>
                    </button>
                </div>

                <!-- Additional Options -->
                <div class="row mt-4">
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-100" onclick="copyDownloadLink()">
                            <i class="fas fa-link me-2"></i>Copy Link
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-light w-100" onclick="window.location.href='{{ route('file.form') }}'">
                            <i class="fas fa-plus me-2"></i>New Link
                        </button>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="mt-5 text-center">
                    <div class="security-badge d-inline-flex align-items-center px-3 py-2">
                        <i class="fas fa-lock me-2 text-success"></i>
                        <span class="small fw-medium">Protected by Captcha • Secure Download • Safe to Use</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Success
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold text-dark">Download Unlocked!</h5>
                <p class="text-muted">Redirecting to download page...</p>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome & Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- hCaptcha -->
<script src="https://js.hcaptcha.com/1/api.js" async defer></script>

<script>
    const fileSlug = '{{ $slug }}';

    function startDownloadProcess() {
        const hcaptchaResponse = document.querySelector('[name="h-captcha-response"]');
        
        if (!hcaptchaResponse || !hcaptchaResponse.value) {
            alert('Please complete the captcha verification first.');
            return;
        }

        verifyAndRedirect();
    }

    async function verifyAndRedirect() {
        const hcaptchaResponse = document.querySelector('[name="h-captcha-response"]');
        const downloadBtn = document.getElementById('downloadBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        // Show loading state
        downloadBtn.disabled = true;
        btnText.textContent = 'VERIFYING...';
        btnSpinner.style.display = 'inline-block';

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
                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Redirect to original link after 2 seconds
                setTimeout(() => {
                    window.open(data.download_url, '_blank');
                    successModal.hide();
                }, 2000);

            } else {
                alert(data.message || 'Verification failed. Please try again.');
                // Reset button
                downloadBtn.disabled = false;
                btnText.textContent = 'UNLOCK DOWNLOAD';
                btnSpinner.style.display = 'none';
                
                // Reset hCaptcha
                if (typeof hcaptcha !== 'undefined') {
                    hcaptcha.reset();
                }
            }
        } catch (error) {
            console.error('Verification error:', error);
            alert('Network error. Please try again.');
            downloadBtn.disabled = false;
            btnText.textContent = 'UNLOCK DOWNLOAD';
            btnSpinner.style.display = 'none';
        }
    }

    function copyDownloadLink() {
        const downloadUrl = window.location.href;
        navigator.clipboard.writeText(downloadUrl).then(() => {
            // Show temporary success message
            const originalText = document.querySelector('button[onclick="copyDownloadLink()"]').innerHTML;
            document.querySelector('button[onclick="copyDownloadLink()"]').innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
            
            setTimeout(() => {
                document.querySelector('button[onclick="copyDownloadLink()"]').innerHTML = originalText;
            }, 2000);
        }).catch(() => {
            alert('Failed to copy link');
        });
    }

    // Add hover effects like generate page
    document.addEventListener('DOMContentLoaded', function() {
        const downloadCard = document.querySelector('.download-card');
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

        // Add hover effects to feature cards
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection