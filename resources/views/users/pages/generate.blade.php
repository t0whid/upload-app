{{-- resources/views/users/pages/generate.blade.php --}}
@extends('users.layouts.master')
@section('title', 'Generate Download Links')
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
        color: var(--text-primary) !important; /* Force text color */
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
        color: var(--text-secondary) !important; /* Force placeholder color */
        opacity: 0.7;
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
                <div class="form-text text-muted mt-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Paste one link or multiple links (max 20), each on a new line
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
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

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const linksForm = document.getElementById('linksForm');
        const generateBtn = document.getElementById('generateBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        if (linksForm) {
            linksForm.addEventListener('submit', function() {
                generateBtn.disabled = true;
                btnText.textContent = 'Processing Links...';
                btnSpinner.style.display = 'inline-block';
            });
        }
    });
</script>
@endsection