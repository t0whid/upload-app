@extends('users.layouts.master')
@section('title', 'Download Files')

@section('content')
<div class="download-page" style="background: #15182F; min-height: 100vh; padding: 40px 0;">
    <div class="container" style="max-width: 800px;">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <div class="icon-container mb-4">
                <div class="icon-wrapper" style="
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    width: 80px;
                    height: 80px;
                    border-radius: 20px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
                    animation: float 3s ease-in-out infinite;
                ">
                    <i class="fa-solid fa-download text-white" style="font-size: 2rem;"></i>
                </div>
            </div>
            <h1 class="text-white mb-3" style="font-weight: 700; font-size: 2.5rem;">Download Your Files</h1>
            <p class="text-light" style="font-size: 1.1rem; opacity: 0.8;">Ready to download your secured files</p>
        </div>

        <!-- Files List -->
        <div class="files-container">
            @foreach ($files as $file)
            <div class="file-card" style="
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 24px;
                margin-bottom: 20px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            ">
                <!-- Background Pattern -->
                <div class="card-pattern" style="
                    position: absolute;
                    top: 0;
                    right: 0;
                    width: 120px;
                    height: 120px;
                    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
                    border-radius: 0 16px 0 100px;
                    z-index: 0;
                "></div>
                
                <div class="file-content" style="position: relative; z-index: 1;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="file-info">
                                <div class="file-icon mb-2">
                                    <i class="fa-solid fa-file" style="
                                        color: #667eea;
                                        font-size: 1.5rem;
                                        margin-right: 12px;
                                    "></i>
                                </div>
                                <h5 class="text-white mb-2" style="font-weight: 600;">{{ $file->filename }}</h5>
                                <div class="file-meta">
                                    <span class="badge" style="
                                        background: rgba(102, 126, 234, 0.2);
                                        color: #667eea;
                                        padding: 6px 12px;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                    ">
                                        <i class="fa-solid fa-hard-drive me-1"></i>
                                        {{ number_format($file->size / 1024 / 1024, 2) }} MB
                                    </span>
                                    <span class="badge ms-2" style="
                                        background: rgba(40, 167, 69, 0.2);
                                        color: #28a745;
                                        padding: 6px 12px;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                    ">
                                        <i class="fa-solid fa-check me-1"></i>
                                        Ready
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="action-buttons">
                                <button class="btn btn-download me-2" onclick="openSingleDownloadPage('{{ $file->slug }}')" style="
                                    background: linear-gradient(135deg, #28a745, #20c997);
                                    border: none;
                                    color: white;
                                    padding: 10px 20px;
                                    border-radius: 10px;
                                    font-weight: 600;
                                    transition: all 0.3s ease;
                                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                                ">
                                    <i class="fa-solid fa-download me-2"></i>
                                    Download
                                </button>
                                <button class="btn btn-copy" onclick="copyDownloadLink('{{ $file->slug }}')" style="
                                    background: rgba(255, 255, 255, 0.1);
                                    border: 1px solid rgba(255, 255, 255, 0.2);
                                    color: #fff;
                                    padding: 10px 15px;
                                    border-radius: 10px;
                                    transition: all 0.3s ease;
                                ">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Stats Section -->
        <div class="stats-section mt-5 text-center">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-item text-white">
                        <h3 style="font-weight: 700; color: #667eea;">{{ count($files) }}</h3>
                        <p style="opacity: 0.8;">Total Files</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item text-white">
                        <h3 style="font-weight: 700; color: #28a745;">{{ number_format(array_sum(array_column($files->toArray(), 'size')) / 1024 / 1024, 1) }} MB</h3>
                        <p style="opacity: 0.8;">Total Size</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item text-white">
                        <h3 style="font-weight: 700; color: #20c997;">100%</h3>
                        <p style="opacity: 0.8;">Secure</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="successToast" class="toast" style="
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        border-radius: 12px;
        color: white;
    ">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
                <i class="fa-solid fa-check-circle me-2" style="font-size: 1.2rem;"></i>
                <span id="successMessage" style="font-weight: 500;">Success</span>
            </div>
            <button type="button" class="btn-close btn-close-white m-3" data-bs-dismiss="toast"></button>
        </div>
    </div>

    <div id="errorToast" class="toast" style="
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        border-radius: 12px;
        color: white;
    ">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
                <i class="fa-solid fa-exclamation-circle me-2" style="font-size: 1.2rem;"></i>
                <span id="errorMessage" style="font-weight: 500;">Error</span>
            </div>
            <button type="button" class="btn-close btn-close-white m-3" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
    function showSuccessToast(message) {
        document.getElementById('successMessage').innerText = message;
        const toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
    }

    function showErrorToast(message) {
        document.getElementById('errorMessage').innerText = message;
        const toast = new bootstrap.Toast(document.getElementById('errorToast'));
        toast.show();
    }

    function copyDownloadLink(slug) {
        const url = `{{ url('/download/single') }}/${slug}`;
        navigator.clipboard.writeText(url)
            .then(() => showSuccessToast('Download link copied to clipboard!'))
            .catch(() => showErrorToast('Failed to copy link'));
    }

    function openSingleDownloadPage(slug) {
        window.open(`{{ url('/download/single') }}/${slug}`, '_blank');
    }

    // Add hover effects
    document.addEventListener('DOMContentLoaded', function() {
        const fileCards = document.querySelectorAll('.file-card');
        fileCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 12px 40px rgba(0, 0, 0, 0.3)';
                this.style.borderColor = 'rgba(102, 126, 234, 0.3)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
                this.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            });
        });

        // Add button hover effects
        const downloadButtons = document.querySelectorAll('.btn-download');
        downloadButtons.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 6px 20px rgba(40, 167, 69, 0.4)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 15px rgba(40, 167, 69, 0.3)';
            });
        });

        const copyButtons = document.querySelectorAll('.btn-copy');
        copyButtons.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255, 255, 255, 0.2)';
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 255, 255, 0.1)';
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .file-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3) !important;
        border-color: rgba(102, 126, 234, 0.3) !important;
    }

    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
    }

    .btn-copy:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        transform: translateY(-2px);
    }

    body {
        background: #151820;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .toast {
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
</style>
@endsection