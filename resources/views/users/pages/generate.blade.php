{{-- resources/views/users/pages/generate.blade.php --}}
@extends('users.layouts.master')
@section('title', 'Generate Download Links')
@section('content')
<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
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
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .generate-card {
        background: var(--card-bg);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        margin: 20px 0;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
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
    }

    .form-icon i {
        font-size: 2rem;
        color: white;
    }

    .form-control {
        background: var(--input-bg);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        font-size: 1rem;
        color: var(--text-primary) !important;
        transition: all 0.3s ease;
        width: 100%;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        outline: none;
        background: var(--input-bg);
        color: var(--text-primary) !important;
    }

    .form-control::placeholder {
        color: var(--text-secondary) !important;
        opacity: 0.7;
    }

    .form-control.warning {
        border-color: var(--warning);
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }

    .form-control.error {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        border-radius: 12px;
        padding: 16px 35px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        color: white;
        cursor: pointer;
        width: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
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
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .alert {
        border-radius: 12px;
        border: none;
        padding: 20px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: var(--text-primary);
    }

    .alert-warning {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: var(--text-primary);
    }

    .text-dark {
        color: var(--text-primary) !important;
    }

    .text-muted {
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

    .form-label {
        color: var(--text-primary) !important;
        font-weight: 600;
    }

    .form-text {
        color: var(--text-secondary) !important;
    }

    .link-count {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        font-size: 0.875rem;
    }

    .link-count .count {
        font-weight: 600;
    }

    .link-count .count.warning {
        color: var(--warning);
    }

    .link-count .count.error {
        color: var(--danger);
    }

    .link-count .count.success {
        color: var(--success);
    }

    .invalid-links-list {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        display: none;
    }

    .invalid-links-list.show {
        display: block;
    }

    .invalid-links-title {
        font-weight: 600;
        color: var(--danger);
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .invalid-link-item {
        display: flex;
        justify-content: between;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px solid rgba(239, 68, 68, 0.2);
    }

    .invalid-link-item:last-child {
        border-bottom: none;
    }

    .invalid-link-url {
        flex: 1;
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        color: var(--text-secondary);
        word-break: break-all;
    }

    .invalid-link-length {
        color: var(--danger);
        font-weight: 600;
        font-size: 0.8rem;
        margin-left: 10px;
    }

    /* Custom Alert Modal */
    .custom-alert-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
    }

    .custom-alert-modal.show {
        display: flex;
    }

    .custom-alert {
        background: var(--card-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        padding: 30px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        animation: alertSlideIn 0.3s ease-out;
    }

    @keyframes alertSlideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .alert-icon {
        text-align: center;
        margin-bottom: 20px;
    }

    .alert-icon i {
        font-size: 3.5rem;
        color: var(--warning);
    }

    .alert-title {
        text-align: center;
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--text-primary);
    }

    .alert-message {
        text-align: center;
        color: var(--text-secondary);
        margin-bottom: 25px;
        line-height: 1.5;
        font-size: 1rem;
    }

    .alert-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-alert {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 1;
    }

    .btn-alert-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-alert-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    }

    .btn-alert-secondary {
        background: var(--input-bg);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-alert-secondary:hover {
        background: var(--border-color);
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        .generate-card {
            margin: 10px 0;
            border-radius: 16px;
        }
        
        .form-icon {
            width: 60px;
            height: 60px;
        }
        
        .form-icon i {
            font-size: 1.5rem;
        }
        
        .btn-primary {
            padding: 14px 25px;
            font-size: 1rem;
        }

        .custom-alert {
            padding: 25px 20px;
            margin: 20px;
        }

        .alert-actions {
            flex-direction: column;
        }

        .btn-alert {
            flex: none;
        }

        .invalid-link-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .invalid-link-length {
            margin-left: 0;
            margin-top: 2px;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 10px;
        }
        
        .generate-card {
            margin: 5px 0;
        }
        
        .form-control {
            padding: 14px 16px;
            font-size: 0.9rem;
        }
    }
</style>

<div class="container">
    <div class="generate-card p-4 p-md-5">
        <!-- Header -->
        <div class="text-center mb-4 mb-md-5">
            <div class="form-icon">
                <i class="fas fa-link"></i>
            </div>
            <h2 class="fw-bold text-dark mb-3">Generate Download Links</h2>
            <p class="text-muted">Paste single or multiple links to create secure downloads</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fa-lg text-danger"></i>
                    <div>
                        <h6 class="alert-heading mb-2 text-danger">Error!</h6>
                        @foreach ($errors->all() as $error)
                            <p class="mb-1">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Single Form for Both -->
        <form method="POST" action="{{ route('file.generate-links') }}" id="linksForm">
            @csrf
            <div class="mb-4">
                <label for="linksInput" class="form-label text-dark fw-semibold mb-3">
                    <i class="fas fa-link me-2"></i>
                    Download Links
                </label>
                <textarea 
                    class="form-control" 
                    id="linksInput" 
                    name="links" 
                    rows="6" 
                    placeholder="Paste single or multiple links (one per line)&#10;Example:&#10;https://example.com/file1.zip&#10;https://example.com/file2.zip"
                    required
                >{{ old('links') }}</textarea>
                
                <!-- Link Count Display -->
                <div class="link-count">
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Maximum 20 links, each link max 100 characters
                    </div>
                    <div class="count" id="linkCount">0 links</div>
                </div>

                <!-- Invalid Links List -->
                <div class="invalid-links-list" id="invalidLinksList">
                    <div class="invalid-links-title">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        Links exceeding 100 characters:
                    </div>
                    <div id="invalidLinksContainer"></div>
                </div>
            </div>

            <div class="d-grid">
                <button type="button" class="btn btn-primary btn-lg" id="generateBtn">
                    <span id="btnText">Generate Download Links</span>
                    <div class="loading-spinner ms-2" id="btnSpinner"></div>
                </button>
            </div>
        </form>

        <!-- Security Info -->
        <div class="mt-4 mt-md-5 text-center">
            <div class="security-badge d-inline-flex align-items-center px-3 py-2">
                <i class="fas fa-lock me-2 text-success"></i>
                <span class="small fw-medium">Secure Connection • Protected by Captcha • Safe to Use</span>
            </div>
        </div>
    </div>
</div>

<!-- Custom Alert Modal -->
<div class="custom-alert-modal" id="customAlertModal">
    <div class="custom-alert">
        <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="alert-title" id="alertTitle">Too Many Links!</div>
        <div class="alert-message" id="alertMessage">
            You have pasted <span id="alertLinkCount" class="fw-bold">0</span> links. 
            The maximum allowed is 20 links per batch.
        </div>
        <div class="alert-actions">
            <button type="button" class="btn-alert btn-alert-secondary" id="alertCancelBtn">
                Cancel
            </button>
            <button type="button" class="btn-alert btn-alert-primary" id="alertProceedBtn">
                Process First 20 Links
            </button>
        </div>
    </div>
</div>

<!-- Character Limit Alert Modal -->
<div class="custom-alert-modal" id="charLimitAlertModal">
    <div class="custom-alert">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle" style="color: var(--danger);"></i>
        </div>
        <div class="alert-title" style="color: var(--danger);">Character Limit Exceeded!</div>
        <div class="alert-message" id="charLimitMessage">
            Some links exceed the 100 character limit. Please shorten the links below 100 characters.
        </div>
        <div class="invalid-links-list show" style="margin: 15px 0; max-height: 200px; overflow-y: auto;">
            <div class="invalid-links-title">
                <i class="fas fa-exclamation-circle me-1"></i>
                Links exceeding 100 characters:
            </div>
            <div id="charLimitInvalidLinks"></div>
        </div>
        <div class="alert-actions">
            <button type="button" class="btn-alert btn-alert-secondary" id="charLimitCancelBtn">
                OK
            </button>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const linksForm = document.getElementById('linksForm');
        const linksInput = document.getElementById('linksInput');
        const linkCount = document.getElementById('linkCount');
        const generateBtn = document.getElementById('generateBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const invalidLinksList = document.getElementById('invalidLinksList');
        const invalidLinksContainer = document.getElementById('invalidLinksContainer');
        
        // Alert modal elements
        const customAlertModal = document.getElementById('customAlertModal');
        const alertTitle = document.getElementById('alertTitle');
        const alertMessage = document.getElementById('alertMessage');
        const alertLinkCount = document.getElementById('alertLinkCount');
        const alertCancelBtn = document.getElementById('alertCancelBtn');
        const alertProceedBtn = document.getElementById('alertProceedBtn');

        // Character limit alert elements
        const charLimitAlertModal = document.getElementById('charLimitAlertModal');
        const charLimitMessage = document.getElementById('charLimitMessage');
        const charLimitInvalidLinks = document.getElementById('charLimitInvalidLinks');
        const charLimitCancelBtn = document.getElementById('charLimitCancelBtn');

        let linkCountValue = 0;
        let currentLinks = [];

        // Function to extract links from text
        function extractLinks(text) {
            const pattern = /https?:\/\/[^\s]+/g;
            const matches = text.match(pattern);
            return matches ? matches : [];
        }

        // Function to check if link exceeds character limit
        function checkLinkCharacterLimit(link) {
            return link.length > 100;
        }

        // Function to get invalid links (exceeding character limit)
        function getInvalidLinks(links) {
            return links.filter(link => checkLinkCharacterLimit(link));
        }

        // Function to update link count and show invalid links
        function updateLinkCount() {
            const text = linksInput.value;
            const links = extractLinks(text);
            linkCountValue = links.length;
            currentLinks = links;
            
            // Update count display
            linkCount.textContent = `${linkCountValue} link${linkCountValue !== 1 ? 's' : ''}`;
            
            // Update count color based on number of links
            if (linkCountValue === 0) {
                linkCount.className = 'count';
                linksInput.classList.remove('warning', 'error');
            } else if (linkCountValue <= 20) {
                linkCount.className = 'count success';
                linksInput.classList.remove('warning', 'error');
            } else if (linkCountValue <= 30) {
                linkCount.className = 'count warning';
                linksInput.classList.remove('error');
                linksInput.classList.add('warning');
            } else {
                linkCount.className = 'count error';
                linksInput.classList.remove('warning');
                linksInput.classList.add('error');
            }

            // Check for invalid links (character limit)
            const invalidLinks = getInvalidLinks(links);
            if (invalidLinks.length > 0) {
                showInvalidLinks(invalidLinks);
            } else {
                hideInvalidLinks();
            }
        }

        // Function to show invalid links list
        function showInvalidLinks(invalidLinks) {
            invalidLinksContainer.innerHTML = '';
            
            invalidLinks.forEach(link => {
                const linkItem = document.createElement('div');
                linkItem.className = 'invalid-link-item';
                linkItem.innerHTML = `
                    <div class="invalid-link-url">${link}</div>
                    <div class="invalid-link-length">${link.length} chars</div>
                `;
                invalidLinksContainer.appendChild(linkItem);
            });
            
            invalidLinksList.classList.add('show');
        }

        // Function to hide invalid links list
        function hideInvalidLinks() {
            invalidLinksList.classList.remove('show');
        }

        // Function to show custom alert for too many links
        function showCustomAlert(linkCount) {
            alertLinkCount.textContent = linkCount;
            customAlertModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Function to show character limit alert
        function showCharLimitAlert(invalidLinks) {
            charLimitInvalidLinks.innerHTML = '';
            
            invalidLinks.forEach(link => {
                const linkItem = document.createElement('div');
                linkItem.className = 'invalid-link-item';
                linkItem.innerHTML = `
                    <div class="invalid-link-url">${link}</div>
                    <div class="invalid-link-length">${link.length} chars</div>
                `;
                charLimitInvalidLinks.appendChild(linkItem);
            });
            
            charLimitAlertModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Function to hide custom alert
        function hideCustomAlert() {
            customAlertModal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Function to hide character limit alert
        function hideCharLimitAlert() {
            charLimitAlertModal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Function to truncate links to first 20
        function truncateToFirst20Links() {
            const text = linksInput.value;
            const lines = text.split('\n');
            let validLines = [];
            let linkCounter = 0;
            
            for (const line of lines) {
                if (line.trim() && line.match(/https?:\/\/[^\s]+/)) {
                    if (linkCounter < 20) {
                        validLines.push(line.trim());
                        linkCounter++;
                    }
                } else if (line.trim()) {
                    validLines.push(line.trim());
                }
            }
            
            linksInput.value = validLines.join('\n');
            updateLinkCount();
        }

        // Function to validate and process form submission
        function processFormSubmission() {
            const text = linksInput.value.trim();
            
            // Basic validation
            if (!text) {
                alert('Please paste at least one download link.');
                return;
            }

            const links = extractLinks(text);
            
            if (links.length === 0) {
                alert('No valid HTTP/HTTPS links found in your input.');
                return;
            }

            // Check for links exceeding character limit
            const invalidLinks = getInvalidLinks(links);
            if (invalidLinks.length > 0) {
                showCharLimitAlert(invalidLinks);
                return;
            }

            // Check if more than 20 links
            if (links.length > 20) {
                showCustomAlert(links.length);
                return;
            }

            // If validation passes, submit the form
            submitForm();
        }

        // Function to submit the form
        function submitForm() {
            // Show loading state
            generateBtn.disabled = true;
            btnText.textContent = 'Processing Links...';
            btnSpinner.style.display = 'inline-block';

            // Submit the form
            linksForm.submit();
        }

        // Event listeners
        if (linksInput) {
            linksInput.addEventListener('input', updateLinkCount);
            linksInput.addEventListener('paste', function(e) {
                setTimeout(updateLinkCount, 100);
            });
            updateLinkCount();
        }

        if (generateBtn) {
            generateBtn.addEventListener('click', processFormSubmission);
        }

        // Alert modal button events
        if (alertCancelBtn) {
            alertCancelBtn.addEventListener('click', hideCustomAlert);
        }

        if (alertProceedBtn) {
            alertProceedBtn.addEventListener('click', function() {
                truncateToFirst20Links();
                hideCustomAlert();
                submitForm();
            });
        }

        // Character limit alert button events
        if (charLimitCancelBtn) {
            charLimitCancelBtn.addEventListener('click', hideCharLimitAlert);
        }

        // Close modals when clicking outside
        [customAlertModal, charLimitAlertModal].forEach(modal => {
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        if (modal === customAlertModal) hideCustomAlert();
                        if (modal === charLimitAlertModal) hideCharLimitAlert();
                    }
                });
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (customAlertModal.classList.contains('show')) hideCustomAlert();
                if (charLimitAlertModal.classList.contains('show')) hideCharLimitAlert();
            }
        });
    });
</script>
@endsection