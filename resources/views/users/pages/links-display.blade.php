{{-- resources/views/users/pages/links-display.blade.php --}}
@extends('users.layouts.master')
@section('title', 'Download Files - ' . $total_files . ' Files')
@section('content')
<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --navbar-bg: #151820;
        --body-bg: #14172C;
        --card-bg: #1E2238;
        --text-primary: #FFFFFF;
        --text-secondary: #94A3B8;
        --border-color: #2D3748;
        --input-bg: #252A41;
    }

    body {
        background: var(--body-bg);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        color: var(--text-primary);
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .downloads-card {
        background: var(--card-bg);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        margin: 20px 0;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .downloads-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    /* Share Section */
    .share-section {
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin: 0 30px 25px;
    }

    .share-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .share-title {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .share-url-container {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .share-url-input {
        flex: 1;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 15px;
        color: var(--text-primary);
        font-size: 0.9rem;
        font-family: 'Courier New', monospace;
    }

    .btn-copy-url {
        background: var(--primary);
        border: none;
        border-radius: 8px;
        padding: 10px 15px;
        color: white;
        font-weight: 500;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }

    .btn-copy-url:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .btn-copy-url.copied {
        background: var(--success);
    }

    /* Bulk Action Section */
    .bulk-action-section {
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 25px;
        margin: 0 30px 25px;
        text-align: center;
    }

    .bulk-action-title {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    .bulk-action-desc {
        color: var(--text-secondary);
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .btn-unlock-all {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        border-radius: 10px;
        padding: 14px 30px;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-unlock-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
    }

    .btn-unlock-all:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Grid Layout - Wider Cards and Centered */
    .files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 25px;
        padding: 0 30px 30px;
        justify-items: center;
    }

    .file-card {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 380px;
    }

    .file-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        border-color: var(--primary);
    }

    .file-header {
        padding: 25px 25px 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .file-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        margin: 0 auto 15px;
        font-size: 1.4rem;
    }

    .file-info {
        text-align: center;
    }

    .file-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 10px;
        font-size: 1rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .file-host {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    .host-favicon {
        width: 16px;
        height: 16px;
        border-radius: 3px;
    }

    .file-content {
        padding: 20px 25px 25px;
    }

    .link-display {
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 12px 15px;
        margin-bottom: 15px;
        display: none;
    }

    .link-text {
        color: var(--text-primary) !important;
        font-size: 0.85rem;
        word-break: break-all;
        font-family: 'Courier New', monospace;
        line-height: 1.4;
    }

    .file-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .btn-unlock {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        border-radius: 10px;
        padding: 14px 20px;
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
    }

    .btn-unlock:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
    }

    .btn-unlock:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-unlocked {
        background: linear-gradient(135deg, #10b981, #059669) !important;
    }

    .btn-copy {
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 12px 20px;
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        display: none;
    }

    .btn-copy:hover {
        background: var(--input-bg);
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-copied {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: white !important;
    }

    .file-count-badge {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 8px 18px;
        border-radius: 20px;
        font-size: 0.9rem;
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

    .header-section h2 {
        color: var(--text-primary) !important;
    }

    .header-section p {
        color: var(--text-secondary) !important;
    }

    .security-badge {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 20px;
        padding: 12px 20px;
        display: inline-flex;
        align-items: center;
    }

    .security-badge span {
        color: var(--text-primary) !important;
    }

    .btn-outline-primary {
        border: 1px solid var(--primary);
        color: var(--primary);
        background: transparent;
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        .share-section, .bulk-action-section {
            margin: 0 20px 20px;
            padding: 20px;
        }
        
        .share-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .share-url-container {
            width: 100%;
        }
        
        .files-grid {
            grid-template-columns: 1fr;
            gap: 15px;
            padding: 0 20px 20px;
        }
        
        .downloads-card {
            margin: 10px 0;
            border-radius: 16px;
        }
        
        .file-card {
            max-width: 100%;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 10px;
        }
        
        .share-section, .bulk-action-section {
            margin: 0 15px 15px;
            padding: 15px;
        }
        
        .files-grid {
            padding: 0 15px 15px;
            gap: 12px;
        }
        
        .file-header {
            padding: 20px 20px 15px;
        }
        
        .file-content {
            padding: 15px 20px 20px;
        }
        
        .file-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }

        .header-section {
            padding: 20px;
        }
    }
</style>

<div class="container">
    <div class="downloads-card">
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
                Click "UNLOCK LINK" to get the download link instantly.
            </p>
        </div>

        <!-- Share Section -->
        <div class="share-section">
            <div class="share-header">
                <div class="share-title">
                    <i class="fas fa-share-alt text-primary"></i>
                    Shareable Link
                </div>
            </div>
            <div class="share-url-container">
                <input type="text" class="share-url-input" id="shareUrlInput" 
                       value="{{ $shareable_url ?? url()->current() }}" readonly>
                <button class="btn-copy-url" onclick="copyShareUrl()" id="copyUrlBtn">
                    <i class="fas fa-copy me-1"></i>
                    <span id="copyUrlText">COPY</span>
                </button>
            </div>
        </div>

        <!-- Bulk Action Section -->
        <div class="bulk-action-section">
            <h4 class="bulk-action-title">
                <i class="fas fa-bolt me-2 text-warning"></i>
                Unlock All Links
            </h4>
            <p class="bulk-action-desc">
                Click to unlock all {{ $total_files }} download links at once
            </p>
            <button class="btn-unlock-all" onclick="unlockAllLinks()" id="bulkUnlockBtn">
                <i class="fas fa-unlock-alt me-1"></i>
                <span id="bulkBtnText">UNLOCK ALL LINKS</span>
                <div class="loading-spinner ms-1" id="bulkBtnSpinner" style="display: none;"></div>
            </button>
        </div>

        <!-- Files Grid -->
        <div class="files-grid">
            @foreach($links as $index => $linkData)
            @php
                $metadata = $linkData['metadata'] ?? [
                    'title' => 'Download File',
                    'site_name' => 'File Host',
                    'host' => parse_url($linkData['original_url'], PHP_URL_HOST) ?? 'unknown',
                    'favicon' => "https://www.google.com/s2/favicons?domain=example.com&sz=32"
                ];
            @endphp
            <div class="file-card">
                <div class="file-header">
                    <div class="file-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="file-info">
                        <div class="file-name" title="{{ $metadata['title'] }}">
                            {{ $metadata['title'] }}
                        </div>
                        <div class="file-host">
                            <img src="{{ $metadata['favicon'] }}" alt="{{ $metadata['site_name'] }}" class="host-favicon" onerror="this.src='https://www.google.com/s2/favicons?domain=example.com&sz=32'">
                            <span>{{ $metadata['site_name'] }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="file-content">
                    <!-- Link Display Area -->
                    <div class="link-display" id="link-display-{{ $linkData['slug'] }}">
                        <div class="link-text" id="link-text-{{ $linkData['slug'] }}"></div>
                    </div>
                    
                    <div class="file-actions">
                        <button class="btn-unlock" onclick="unlockLink('{{ $linkData['slug'] }}')" id="btn-{{ $linkData['slug'] }}">
                            <i class="fas fa-lock-open me-1"></i>
                            <span id="btn-text-{{ $linkData['slug'] }}">UNLOCK LINK</span>
                            <div class="loading-spinner ms-1" id="spinner-{{ $linkData['slug'] }}" style="display: none;"></div>
                        </button>
                        
                        <button class="btn-copy" onclick="copyDownloadLink('{{ $linkData['slug'] }}')" id="copy-btn-{{ $linkData['slug'] }}">
                            <i class="fas fa-copy me-1"></i>
                            <span id="copy-text-{{ $linkData['slug'] }}">COPY LINK</span>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="header-section text-center">
            <div class="security-badge d-inline-flex align-items-center px-3 py-2 mb-3">
                <i class="fas fa-shield-alt me-2 text-success"></i>
                <span class="small fw-medium">Secure Download Links • Direct Access</span>
            </div>
            
            <a href="{{ route('file.form') }}" class="btn btn-outline-primary">
                <i class="fas fa-plus me-2"></i>
                Generate New Links
            </a>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast align-items-center text-white bg-success border-0" id="successToast">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="toastMessage">Link unlocked successfully!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Font Awesome & Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const successToast = new bootstrap.Toast(document.getElementById('successToast'));

    // Unlock single link
    async function unlockLink(slug) {
        const unlockBtn = document.getElementById(`btn-${slug}`);
        const btnText = document.getElementById(`btn-text-${slug}`);
        const spinner = document.getElementById(`spinner-${slug}`);

        // Show loading state
        unlockBtn.disabled = true;
        btnText.textContent = 'UNLOCKING...';
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
                    slug: slug
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Update UI to show unlocked state
                unlockBtn.innerHTML = '<i class="fas fa-check me-1"></i><span>UNLOCKED</span>';
                unlockBtn.classList.add('btn-unlocked');
                unlockBtn.disabled = true;
                
                // Show link and copy button
                const linkText = document.getElementById(`link-text-${slug}`);
                const linkDisplay = document.getElementById(`link-display-${slug}`);
                const copyBtn = document.getElementById(`copy-btn-${slug}`);
                
                linkText.textContent = data.download_url;
                linkDisplay.style.display = 'block';
                copyBtn.style.display = 'flex';
                
                // Hide spinner
                spinner.style.display = 'none';
                
                // Show success toast
                document.getElementById('toastMessage').textContent = 'Link unlocked successfully!';
                successToast.show();
                
            } else {
                alert(data.message || 'Failed to unlock link. Please try again.');
                resetButton(slug);
            }
        } catch (error) {
            console.error('Unlock error:', error);
            alert('Network error. Please try again.');
            resetButton(slug);
        }
    }

    // Unlock all links
    async function unlockAllLinks() {
        const bulkUnlockBtn = document.getElementById('bulkUnlockBtn');
        const bulkBtnText = document.getElementById('bulkBtnText');
        const bulkBtnSpinner = document.getElementById('bulkBtnSpinner');

        // Show loading state
        bulkUnlockBtn.disabled = true;
        bulkBtnText.textContent = 'UNLOCKING ALL...';
        bulkBtnSpinner.style.display = 'inline-block';

        try {
            const response = await fetch('{{ route('file.verify-download-all') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    batch_id: '{{ $batch_id }}'
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Unlock all links in the UI
                data.download_links.forEach(linkData => {
                    const unlockBtn = document.getElementById(`btn-${linkData.slug}`);
                    const copyBtn = document.getElementById(`copy-btn-${linkData.slug}`);
                    const linkDisplay = document.getElementById(`link-display-${linkData.slug}`);
                    const linkText = document.getElementById(`link-text-${linkData.slug}`);

                    if (unlockBtn) {
                        unlockBtn.innerHTML = '<i class="fas fa-check me-1"></i><span>UNLOCKED</span>';
                        unlockBtn.classList.add('btn-unlocked');
                        unlockBtn.disabled = true;
                    }
                    
                    if (linkText && linkDisplay) {
                        linkText.textContent = linkData.download_url;
                        linkDisplay.style.display = 'block';
                    }
                    
                    if (copyBtn) {
                        copyBtn.style.display = 'flex';
                    }
                });

                // Update bulk button
                bulkUnlockBtn.innerHTML = '<i class="fas fa-check me-1"></i><span>ALL UNLOCKED</span>';
                bulkUnlockBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                bulkUnlockBtn.disabled = true;
                
                // Hide spinner
                bulkBtnSpinner.style.display = 'none';
                
                // Show success toast
                document.getElementById('toastMessage').textContent = 'All links unlocked successfully!';
                successToast.show();
                
            } else {
                alert(data.message || 'Failed to unlock links. Please try again.');
                resetBulkButton();
            }
        } catch (error) {
            console.error('Bulk unlock error:', error);
            alert('Network error. Please try again.');
            resetBulkButton();
        }
    }

    function resetButton(slug) {
        const unlockBtn = document.getElementById(`btn-${slug}`);
        const btnText = document.getElementById(`btn-text-${slug}`);
        const spinner = document.getElementById(`spinner-${slug}`);
        
        if (unlockBtn) {
            unlockBtn.disabled = false;
            unlockBtn.innerHTML = '<i class="fas fa-lock-open me-1"></i><span>UNLOCK LINK</span>';
            unlockBtn.classList.remove('btn-unlocked');
        }
        
        if (btnText) {
            btnText.textContent = 'UNLOCK LINK';
        }
        
        if (spinner) {
            spinner.style.display = 'none';
        }
    }

    function resetBulkButton() {
        const bulkUnlockBtn = document.getElementById('bulkUnlockBtn');
        const bulkBtnText = document.getElementById('bulkBtnText');
        const bulkBtnSpinner = document.getElementById('bulkBtnSpinner');
        
        if (bulkUnlockBtn) {
            bulkUnlockBtn.disabled = false;
            bulkUnlockBtn.innerHTML = '<i class="fas fa-unlock-alt me-1"></i><span>UNLOCK ALL LINKS</span>';
            bulkUnlockBtn.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
        }
        
        if (bulkBtnText) {
            bulkBtnText.textContent = 'UNLOCK ALL LINKS';
        }
        
        if (bulkBtnSpinner) {
            bulkBtnSpinner.style.display = 'none';
        }
    }

    // Copy functions
    function copyShareUrl() {
        const shareUrlInput = document.getElementById('shareUrlInput');
        const copyUrlBtn = document.getElementById('copyUrlBtn');
        
        shareUrlInput.select();
        shareUrlInput.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(shareUrlInput.value).then(() => {
            copyUrlBtn.classList.add('copied');
            copyUrlBtn.innerHTML = '<i class="fas fa-check me-1"></i><span>COPIED!</span>';
            
            document.getElementById('toastMessage').textContent = 'Shareable link copied to clipboard!';
            successToast.show();
            
            setTimeout(() => {
                copyUrlBtn.classList.remove('copied');
                copyUrlBtn.innerHTML = '<i class="fas fa-copy me-1"></i><span>COPY</span>';
            }, 2000);
            
        }).catch(() => {
            alert('Failed to copy link');
        });
    }

    function copyDownloadLink(slug) {
        const linkText = document.getElementById(`link-text-${slug}`);
        const copyBtn = document.getElementById(`copy-btn-${slug}`);
        
        if (linkText && linkText.textContent) {
            navigator.clipboard.writeText(linkText.textContent).then(() => {
                copyBtn.classList.add('btn-copied');
                copyBtn.innerHTML = '<i class="fas fa-check me-1"></i><span>COPIED!</span>';
                
                document.getElementById('toastMessage').textContent = 'Link copied to clipboard!';
                successToast.show();
                
                setTimeout(() => {
                    copyBtn.classList.remove('btn-copied');
                    copyBtn.innerHTML = '<i class="fas fa-copy me-1"></i><span>COPY LINK</span>';
                }, 2000);
                
            }).catch(() => {
                alert('Failed to copy link');
            });
        }
    }

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