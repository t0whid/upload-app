@extends('users.layouts.master')
@section('title', 'Generate Download Link')
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

    .generate-card {
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

    .generate-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .form-icon {
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

    .form-icon::after {
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

    .form-icon i {
        font-size: 2rem;
        color: white;
        z-index: 2;
    }

    .form-control {
        background: var(--input-bg);
        border: 2px solid var(--border-color);
        border-radius: 14px;
        padding: 16px 20px;
        font-size: 1rem;
        color: var(--text-primary);
        transition: all 0.3s ease;
        width: 100%;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background: var(--input-bg);
        color: var(--text-primary);
        outline: none;
    }

    .form-control::placeholder {
        color: var(--text-secondary);
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

    .feature-card {
        background: var(--bg-secondary);
        border-radius: 16px;
        border-left: 4px solid var(--primary);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        padding: 20px;
        text-align: center;
        height: 100%;
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

    .generate-card {
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

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: var(--text-primary);
    }

    /* Text styling */
    .text-dark {
        color: var(--text-primary) !important;
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .form-text {
        display: block;
        margin-top: 8px;
        font-size: 0.875rem;
    }

    /* Grid system fixes */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .col-md-4, .col-md-8, .col-lg-6 {
        padding: 0 10px;
        box-sizing: border-box;
    }

    .col-md-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }

    .col-md-8 {
        flex: 0 0 66.666667%;
        max-width: 66.666667%;
    }

    .col-lg-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 15px;
        }
        
        .generate-card {
            padding: 30px 20px !important;
            margin: 10px 0;
        }
        
        body {
            padding: 10px;
        }
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

    .mb-2 {
        margin-bottom: 0.5rem !important;
    }

    .ms-2 {
        margin-left: 0.5rem !important;
    }

    .me-2 {
        margin-right: 0.5rem !important;
    }

    .me-3 {
        margin-right: 1rem !important;
    }

    .me-1 {
        margin-right: 0.25rem !important;
    }

    .g-3 {
        gap: 1rem !important;
    }

    .text-center {
        text-align: center !important;
    }

    .fw-bold {
        font-weight: 700 !important;
    }

    .fw-semibold {
        font-weight: 600 !important;
    }

    .d-flex {
        display: flex !important;
    }

    .d-grid {
        display: grid !important;
    }

    .align-items-center {
        align-items: center !important;
    }

    .d-inline-flex {
        display: inline-flex !important;
    }

    .justify-content-center {
        justify-content: center !important;
    }

    .fa-lg {
        font-size: 1.33333em !important;
    }

    .small {
        font-size: 0.875rem !important;
    }

    .alert-heading {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
</style>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-5">
        <div class="col-md-8 col-lg-6">
            <div class="generate-card p-5">
                <!-- Header -->
                <div class="text-center mb-5">
                    <div class="form-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-3" style="font-size: 2rem;">Generate Download Link</h2>
                    <p class="text-muted" style="font-size: 1.1rem;">Paste your 1fichier link below to create a secure download</p>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                <div class="alert alert-success mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-lg text-success"></i>
                        <div>
                            <h6 class="alert-heading mb-2 text-success">Success!</h6>
                            <p class="mb-0">{{ session('success') }}</p>
{{--                             @if (session('bulk_slugs'))
                            <p class="mb-0 small mt-2">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Download pages are opening in new tabs...
                            </p>
                            @endif --}}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                        <div>
                            <h6 class="alert-heading mb-2">Please fix the following errors:</h6>
                            @foreach ($errors->all() as $error)
                            <p class="mb-1 small">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Failed Links -->
                @if (session('bulk_failed_links'))
                <div class="alert alert-danger mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
                        <div>
                            <h6 class="alert-heading mb-2">Some links failed to process:</h6>
                            @foreach (session('bulk_failed_links') as $failedLink)
                            <p class="mb-1 small">
                                <strong>URL:</strong> {{ Str::limit($failedLink['url'], 50) }}<br>
                                <strong>Error:</strong> {{ $failedLink['error'] }}
                            </p>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Single Link Form -->
                <form method="POST" action="{{ route('file.generate') }}" id="generateForm">
                    @csrf
                    <div class="mb-4">
                        <label for="linkInput" class="form-label text-dark fw-semibold mb-3">Single 1Fichier Link</label>
                        <input 
                            type="url" 
                            class="form-control form-control-lg" 
                            id="linkInput"
                            name="link" 
                            placeholder="https://1fichier.com/..." 
                            required
                            value="{{ old('link') }}"
                        >
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Paste a single 1fichier download link
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                            <span id="btnText">Generate Download Link</span>
                            <div class="loading-spinner ms-2" id="btnSpinner"></div>
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="my-4 text-center">
                    <span class="text-muted">OR</span>
                </div>

                <!-- Bulk Links Section -->
                <div class="mt-4">
                    <div class="feature-card">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fas fa-layer-group me-2 text-primary"></i>
                            Bulk Links Processing
                        </h5>
                        <form method="POST" action="{{ route('file.generate-bulk') }}" id="bulkForm">
                            @csrf
                            <div class="mb-3">
                                <label for="bulkLinks" class="form-label text-dark fw-semibold">Multiple Links (One per line)</label>
                                <textarea 
                                    class="form-control" 
                                    id="bulkLinks"
                                    name="bulk_links" 
                                    rows="6" 
                                    placeholder="Paste multiple 1fichier links, one per line...&#10;Example:&#10;https://1fichier.com/?abc123&#10;https://1fichier.com/?xyz789"
                                >{{ old('bulk_links') }}</textarea>
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Paste multiple links (max 20), each on a new line. Each link will open in a new tab.
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="bulkBtn">
                                    <span id="bulkBtnText">Process Multiple Links</span>
                                    <div class="loading-spinner ms-2" id="bulkBtnSpinner"></div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="mt-5 text-center">
                    <div class="security-badge d-inline-flex align-items-center px-3 py-2">
                        <i class="fas fa-lock me-2 text-success"></i>
                        <span class="small fw-medium">Secure Connection • Privacy Protected • No Storage</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generateForm = document.getElementById('generateForm');
        const generateBtn = document.getElementById('generateBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        const bulkForm = document.getElementById('bulkForm');
        const bulkBtn = document.getElementById('bulkBtn');
        const bulkBtnText = document.getElementById('bulkBtnText');
        const bulkBtnSpinner = document.getElementById('bulkBtnSpinner');

        if (generateForm) {
            generateForm.addEventListener('submit', function() {
                // Show loading state
                generateBtn.disabled = true;
                btnText.textContent = 'Generating...';
                btnSpinner.style.display = 'inline-block';
            });
        }

        if (bulkForm) {
            bulkForm.addEventListener('submit', function() {
                // Show loading state
                bulkBtn.disabled = true;
                bulkBtnText.textContent = 'Processing Links...';
                bulkBtnSpinner.style.display = 'inline-block';
            });
        }

        // Open bulk download pages with user interaction
        @if (session('bulk_slugs'))
            const slugs = @json(session('bulk_slugs'));
            const downloadLinksContainer = document.createElement('div');
            downloadLinksContainer.className = 'download-links-container mt-4 p-4 bg-light rounded';
            
            let linksHTML = `
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-external-link-alt me-2"></i>
                        Download Links Ready!
                    </h6>
                    <p class="mb-3">Click the links below to open download pages in new tabs:</p>
                    <div class="d-grid gap-2">
            `;
            
            slugs.forEach((slug, index) => {
                const downloadUrl = "{{ route('file.download', ':slug') }}".replace(':slug', slug);
                linksHTML += `
                    <a href="${downloadUrl}" target="_blank" class="btn btn-outline-primary btn-sm text-start">
                        <i class="fas fa-download me-2"></i>
                        Download File ${index + 1}
                    </a>
                `;
            });
            
            /* linksHTML += `
                    </div>
                    <div class="mt-3">
                        <button onclick="openAllTabs()" class="btn btn-success btn-sm me-2">
                            <i class="fas fa-external-link-alt me-1"></i> Open All Tabs
                        </button>
                        <button onclick="copyAllLinks()" class="btn btn-secondary btn-sm">
                            <i class="fas fa-copy me-1"></i> Copy All Links
                        </button>
                    </div>
                </div>
            `; */
            
            downloadLinksContainer.innerHTML = linksHTML;
            
            // Find the success alert and append the links container after it
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                successAlert.parentNode.insertBefore(downloadLinksContainer, successAlert.nextSibling);
            }

            // Function to open all tabs (with user interaction)
            window.openAllTabs = function() {
                slugs.forEach((slug, index) => {
                    const downloadUrl = "{{ route('file.download', ':slug') }}".replace(':slug', slug);
                    setTimeout(() => {
                        window.open(downloadUrl, '_blank');
                    }, index * 500); // 500ms delay between each tab
                });
            };

            // Function to copy all links
            window.copyAllLinks = function() {
                const links = slugs.map(slug => {
                    return "{{ route('file.download', ':slug') }}".replace(':slug', slug);
                }).join('\n');
                
                navigator.clipboard.writeText(links).then(() => {
                    // Show temporary success message
                    const copyBtn = document.querySelector('button[onclick="copyAllLinks()"]');
                    const originalText = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
                    copyBtn.classList.remove('btn-secondary');
                    copyBtn.classList.add('btn-success');
                    
                    setTimeout(() => {
                        copyBtn.innerHTML = originalText;
                        copyBtn.classList.remove('btn-success');
                        copyBtn.classList.add('btn-secondary');
                    }, 2000);
                }).catch(() => {
                    alert('Failed to copy links');
                });
            };
            
            // Clear the session data
            fetch("{{ route('file.form') }}", {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        @endif

        // Add hover effects
        const generateCard = document.querySelector('.generate-card');
        if (generateCard) {
            generateCard.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 35px 60px var(--shadow-color)';
            });

            generateCard.addEventListener('mouseleave', function() {
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