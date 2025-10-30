@extends('users.layouts.master')
@section('title', 'Bulk Download')
@section('content')
<style>
    /* Your existing CSS + additional styles for bulk download */
    .bulk-download-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 0 25px 50px var(--shadow-color);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
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

    .file-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .file-item {
        background: var(--file-info-bg);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid var(--primary);
        transition: all 0.3s ease;
    }

    .file-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px var(--shadow-color);
    }

    .batch-info {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(79, 70, 229, 0.1));
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .success-badge {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.8rem;
    }

    .error-badge {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #ef4444;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.8rem;
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
    }

    .btn-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="bulk-download-card p-5">
                <!-- Header -->
                <div class="text-center mb-5">
                    <div class="file-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-3">Bulk Download Ready</h2>
                    <p class="text-muted">{{ count($successfulLinks) }} files processed successfully</p>
                </div>

                <!-- Batch Info -->
                <div class="batch-info mb-4">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h4 class="fw-bold text-dark">{{ count($successfulLinks) }}</h4>
                            <p class="text-muted mb-0">Successful</p>
                        </div>
                        <div class="col-md-4">
                            <h4 class="fw-bold text-dark">{{ count($failedLinks) }}</h4>
                            <p class="text-muted mb-0">Failed</p>
                        </div>
                        <div class="col-md-4">
                            <h4 class="fw-bold text-dark">{{ count($successfulLinks) + count($failedLinks) }}</h4>
                            <p class="text-muted mb-0">Total</p>
                        </div>
                    </div>
                </div>

                <!-- Successful Downloads -->
                @if(count($successfulLinks) > 0)
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Ready for Download ({{ count($successfulLinks) }})
                    </h5>
                    <div class="file-list">
                        @foreach($successfulLinks as $index => $link)
                        <div class="file-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1">File {{ $index + 1 }}</h6>
                                    <p class="text-muted small mb-0 text-truncate" style="max-width: 400px;">
                                        {{ $link['original_url'] }}
                                    </p>
                                </div>
                                <div class="ms-3 d-flex align-items-center">
                                    <span class="success-badge me-2">Ready</span>
                                    <button class="btn btn-primary btn-sm" onclick="downloadSingleFile('{{ $link['slug'] }}')">
                                        <i class="fas fa-download me-1"></i>Download
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Failed Downloads -->
                @if(count($failedLinks) > 0)
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                        Failed to Process ({{ count($failedLinks) }})
                    </h5>
                    <div class="file-list">
                        @foreach($failedLinks as $index => $link)
                        <div class="file-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1">File {{ $index + 1 }}</h6>
                                    <p class="text-muted small mb-0 text-truncate" style="max-width: 400px;">
                                        {{ $link['url'] }}
                                    </p>
                                    <p class="text-danger small mb-0 mt-1">{{ $link['error'] }}</p>
                                </div>
                                <div class="ms-3">
                                    <span class="error-badge">Failed</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="d-grid gap-2 mt-4">
                    @if(count($successfulLinks) > 0)
                    <button class="btn btn-success" onclick="downloadAllFiles()">
                        <i class="fas fa-download me-2"></i>Download All Files ({{ count($successfulLinks) }})
                    </button>
                    @endif
                    <button class="btn btn-outline-secondary" onclick="window.location.href='{{ route('file.form') }}'">
                        <i class="fas fa-plus me-2"></i>Process More Links
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

<!-- hCaptcha Modal -->
<div class="modal fade" id="hcaptchaModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify You're Human</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="h-captcha" data-sitekey="<?php echo env('HCAPTCHA_SITE_KEY'); ?>"></div>
                <input type="hidden" id="currentFileSlug">

                <button type="button" class="btn btn-success w-100 mt-3" onclick="verifyAndDownload()" id="verifyBtn">
                    <i class="fa-solid fa-check"></i> Verify & Download
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://js.hcaptcha.com/1/api.js" async defer></script>

<script>
    let currentSlug = '';
    const fileSlugs = @json(array_column($successfulLinks, 'slug'));
    let currentDownloadIndex = 0;

    function downloadSingleFile(slug) {
        currentSlug = slug;
        showHCaptchaModal();
    }

    function downloadAllFiles() {
        if (fileSlugs.length === 0) return;
        
        currentDownloadIndex = 0;
        currentSlug = fileSlugs[currentDownloadIndex];
        showHCaptchaModal();
    }

    function showHCaptchaModal() {
        if (typeof hcaptcha !== 'undefined') {
            hcaptcha.reset();
        }
        document.getElementById('currentFileSlug').value = currentSlug;
        const modal = new bootstrap.Modal(document.getElementById('hcaptchaModal'));
        modal.show();
    }

    async function verifyAndDownload() {
        const hcaptchaResponse = document.querySelector('[name="h-captcha-response"]');
        
        if (!hcaptchaResponse || !hcaptchaResponse.value) {
            alert('Please complete the captcha verification');
            return;
        }

        const verifyBtn = document.getElementById('verifyBtn');
        const originalText = verifyBtn.innerHTML;
        
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
                    slug: currentSlug
                })
            });

            const data = await response.json();

            if (data.success) {
                // Create invisible iframe for download
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = data.download_url;
                document.body.appendChild(iframe);

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('hcaptchaModal'));
                if (modal) modal.hide();
                
                // Remove downloaded file from queue and download next if in bulk mode
                if (fileSlugs.length > 0 && currentDownloadIndex < fileSlugs.length) {
                    currentDownloadIndex++;
                    if (currentDownloadIndex < fileSlugs.length) {
                        // Auto-download next file after 2 seconds
                        setTimeout(() => {
                            currentSlug = fileSlugs[currentDownloadIndex];
                            showHCaptchaModal();
                        }, 2000);
                    }
                }

                // Reset hCaptcha
                if (typeof hcaptcha !== 'undefined') {
                    hcaptcha.reset();
                }
            } else {
                alert(data.message || 'Captcha verification failed');
                if (typeof hcaptcha !== 'undefined') {
                    hcaptcha.reset();
                }
            }
        } catch (error) {
            console.error('Captcha verification error:', error);
            alert('Network error. Please try again.');
            if (typeof hcaptcha !== 'undefined') {
                hcaptcha.reset();
            }
        } finally {
            verifyBtn.innerHTML = originalText;
            verifyBtn.disabled = false;
        }
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Bulk download page loaded with ' + fileSlugs.length + ' files');
    });
</script>
@endsection