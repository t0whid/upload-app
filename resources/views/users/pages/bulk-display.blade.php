{{-- resources/views/users/pages/bulk-display.blade.php --}}
@extends('users.layouts.master')
@section('title', 'Download Files - ' . $total_files . ' Files')
@section('content')
<style>
    /* MultiUp-like styling */
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
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
        margin: 0;
        padding: 20px;
        box-sizing: border-box;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .bulk-download-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 0 25px 50px var(--shadow-color);
        border: 1px solid rgba(255, 255, 255, 0.15);
        margin: 20px 0;
        overflow: hidden;
        position: relative;
    }

    .bulk-download-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .file-item {
        background: var(--bg-secondary);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .file-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px var(--shadow-color);
    }

    .file-item-header {
        padding: 16px 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .file-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary);
        color: white;
        flex-shrink: 0;
    }

    .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-host {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .host-favicon {
        width: 16px;
        height: 16px;
        border-radius: 2px;
    }

    .file-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-download {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        color: white;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-download:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-download:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .captcha-container {
        background: var(--bg-secondary);
        border-radius: 12px;
        padding: 20px;
        margin: 15px 0;
        display: none;
    }

    .file-item.expanded .captcha-container {
        display: block;
    }

    .file-count-badge {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .loading-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .header-section {
        padding: 30px;
        border-bottom: 1px solid var(--border-color);
    }

    .files-section {
        padding: 20px 30px;
    }

    .security-badge {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 20px;
        padding: 12px 20px;
        backdrop-filter: blur(10px);
        display: inline-flex;
        align-items: center;
    }

    @media (max-width: 768px) {
        .file-item-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .file-actions {
            width: 100%;
            justify-content: flex-end;
        }
        
        .bulk-download-card {
            margin: 10px 0;
        }
        
        body {
            padding: 10px;
        }
    }
</style>

<div class="container">
    <div class="bulk-download-card">
        <!-- Header -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold text-dark mb-0">
                    <i class="fas fa-download me-2 text-primary"></i>
                    Download Files
                </h2>
                <span class="file-count-badge">
                    {{ $total_files }} File{{ $total_files > 1 ? 's' : '' }}
                </span>
            </div>
            <p class="text-muted mb-0">
                Click on any file to unlock download. All links are protected by captcha verification.
            </p>
        </div>

        <!-- Files List -->
        <div class="files-section">
            @foreach($links as $index => $linkData)
            @php
                // Metadata is already an array, no need to decode
                $metadata = $linkData['metadata'] ?? [
                    'title' => 'Download File',
                    'site_name' => 'File Host',
                    'host' => parse_url($linkData['original_url'], PHP_URL_HOST),
                    'favicon' => "https://www.google.com/s2/favicons?domain=example.com&sz=32"
                ];
            @endphp
            <div class="file-item" id="file-{{ $linkData['slug'] }}">
                <div class="file-item-header" onclick="toggleFileItem('{{ $linkData['slug'] }}')">
                    <div class="file-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="file-info">
                        <div class="file-name">
                            {{ $metadata['title'] }}
                        </div>
                        <div class="file-host">
                            <img src="{{ $metadata['favicon'] }}" alt="{{ $metadata['site_name'] }}" class="host-favicon" onerror="this.src='https://www.google.com/s2/favicons?domain=example.com&sz=32'">
                            <span>{{ $metadata['site_name'] }} • {{ $metadata['host'] }}</span>
                        </div>
                    </div>
                    <div class="file-actions">
                        <button class="btn-download" onclick="event.stopPropagation(); startDownloadProcess('{{ $linkData['slug'] }}')" id="btn-{{ $linkData['slug'] }}">
                            <i class="fas fa-lock-open me-1"></i>
                            <span id="btn-text-{{ $linkData['slug'] }}">UNLOCK</span>
                            <div class="loading-spinner ms-1" id="spinner-{{ $linkData['slug'] }}" style="display: none;"></div>
                        </button>
                    </div>
                </div>
                
                <!-- Captcha Container -->
                <div class="captcha-container" id="captcha-{{ $linkData['slug'] }}">
                    <div class="text-center mb-3">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="fas fa-robot me-2 text-primary"></i>
                            Verify You're Human
                        </h6>
                        <p class="text-muted small mb-3">Complete the captcha to unlock this download</p>
                    </div>
                    
                    <div class="h-captcha" data-sitekey="<?php echo env('HCAPTCHA_SITE_KEY'); ?>" id="hcaptcha-{{ $linkData['slug'] }}"></div>
                    
                    <div class="text-center mt-3">
                        <button class="btn-download" onclick="verifyAndDownload('{{ $linkData['slug'] }}')" id="verify-btn-{{ $linkData['slug'] }}">
                            <i class="fas fa-check me-1"></i>
                            VERIFY & DOWNLOAD
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="header-section text-center">
            <div class="security-badge d-inline-flex align-items-center px-3 py-2">
                <i class="fas fa-lock me-2 text-success"></i>
                <span class="small fw-medium">All downloads protected by Captcha • Secure Connection</span>
            </div>
            
            <!-- New Link Button -->
            <div class="mt-3">
                <a href="{{ route('file.form') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus me-2"></i>
                    Generate New Links
                </a>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold text-dark mb-2">Download Unlocked!</h5>
                <p class="text-muted mb-0">Opening download page...</p>
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
    let currentActiveSlug = null;

    function toggleFileItem(slug) {
        const fileItem = document.getElementById(`file-${slug}`);
        const captchaContainer = document.getElementById(`captcha-${slug}`);
        
        // If clicking on already expanded item, do nothing
        if (fileItem.classList.contains('expanded')) {
            return;
        }
        
        // Collapse previously active item
        if (currentActiveSlug && currentActiveSlug !== slug) {
            const prevFileItem = document.getElementById(`file-${currentActiveSlug}`);
            const prevCaptcha = document.getElementById(`captcha-${currentActiveSlug}`);
            if (prevFileItem) {
                prevFileItem.classList.remove('expanded');
            }
            if (prevCaptcha) {
                prevCaptcha.style.display = 'none';
            }
        }
        
        // Expand current item
        fileItem.classList.add('expanded');
        captchaContainer.style.display = 'block';
        currentActiveSlug = slug;
        
        // Reset hCaptcha for this item if needed
        if (typeof hcaptcha !== 'undefined') {
            setTimeout(() => {
                // hCaptcha will auto-render when container becomes visible
            }, 100);
        }
    }

    function startDownloadProcess(slug) {
        // Ensure the item is expanded
        toggleFileItem(slug);
        
        // Scroll to the captcha section
        document.getElementById(`captcha-${slug}`).scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
    }

    async function verifyAndDownload(slug) {
        const hcaptchaResponse = document.querySelector(`#hcaptcha-${slug} [name="h-captcha-response"]`);
        const verifyBtn = document.getElementById(`verify-btn-${slug}`);
        const downloadBtn = document.getElementById(`btn-${slug}`);
        const btnText = document.getElementById(`btn-text-${slug}`);
        const spinner = document.getElementById(`spinner-${slug}`);
        
        if (!hcaptchaResponse || !hcaptchaResponse.value) {
            alert('Please complete the captcha verification first.');
            return;
        }

        // Show loading state
        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> VERIFYING...';
        
        downloadBtn.disabled = true;
        btnText.textContent = 'VERIFYING...';
        spinner.style.display = 'inline-block';

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
                    slug: slug
                })
            });

            const data = await response.json();

            if (data.success) {
                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Update button to show success
                downloadBtn.innerHTML = '<i class="fas fa-check me-1"></i><span>UNLOCKED</span>';
                downloadBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                downloadBtn.disabled = true;
                
                // Close the expanded section
                fileItem.classList.remove('expanded');
                captchaContainer.style.display = 'none';
                
                // Redirect to original link after 2 seconds
                setTimeout(() => {
                    window.open(data.download_url, '_blank');
                    successModal.hide();
                }, 2000);

            } else {
                alert(data.message || 'Verification failed. Please try again.');
                // Reset buttons
                resetButtons(slug);
                
                // Reset hCaptcha
                if (typeof hcaptcha !== 'undefined') {
                    hcaptcha.reset(document.querySelector(`#hcaptcha-${slug}`));
                }
            }
        } catch (error) {
            console.error('Verification error:', error);
            alert('Network error. Please try again.');
            resetButtons(slug);
        }
    }

    function resetButtons(slug) {
        const verifyBtn = document.getElementById(`verify-btn-${slug}`);
        const downloadBtn = document.getElementById(`btn-${slug}`);
        const btnText = document.getElementById(`btn-text-${slug}`);
        const spinner = document.getElementById(`spinner-${slug}`);
        
        if (verifyBtn) {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="fas fa-check me-1"></i> VERIFY & DOWNLOAD';
        }
        
        if (downloadBtn) {
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = '<i class="fas fa-lock-open me-1"></i><span>UNLOCK</span>';
            downloadBtn.style.background = 'linear-gradient(135deg, var(--primary), var(--primary-dark))';
        }
        
        if (btnText) {
            btnText.textContent = 'UNLOCK';
        }
        
        if (spinner) {
            spinner.style.display = 'none';
        }
    }

    // Close all file items when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.file-item')) {
            const expandedItems = document.querySelectorAll('.file-item.expanded');
            expandedItems.forEach(item => {
                item.classList.remove('expanded');
                const slug = item.id.replace('file-', '');
                const captcha = document.getElementById(`captcha-${slug}`);
                if (captcha) {
                    captcha.style.display = 'none';
                }
            });
            currentActiveSlug = null;
        }
    });

    // Handle favicon loading errors
    document.addEventListener('DOMContentLoaded', function() {
        const favicons = document.querySelectorAll('.host-favicon');
        favicons.forEach(favicon => {
            favicon.onerror = function() {
                this.src = 'https://www.google.com/s2/favicons?domain=example.com&sz=32';
            };
        });
    });
</script>
@endsection