@extends('users.layouts.master')
@section('title', 'Multi Upload')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="upload-container" style="background: #15182F; min-height: 100vh; padding: 60px 20px;">
        <div class="container" style="max-width: 700px;">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <div class="upload-icon mb-4">
                    <div class="icon-wrapper" style="
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        width: 100px;
                        height: 100px;
                        border-radius: 25px;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
                        animation: float 3s ease-in-out infinite;
                        position: relative;
                        overflow: hidden;
                    ">
                        <i class="fa-solid fa-cloud-upload-alt text-white" style="font-size: 2.5rem; z-index: 2;"></i>
                        <div class="icon-shine" style="
                            position: absolute;
                            top: -50%;
                            left: -50%;
                            width: 200%;
                            height: 200%;
                            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
                            transform: rotate(45deg);
                        "></div>
                    </div>
                </div>
                <h1 class="text-white mb-3" style="font-weight: 700; font-size: 2.8rem; letter-spacing: -0.5px;">
                    Secure File Hosting
                </h1>
                <p class="text-light" style="font-size: 1.2rem; opacity: 0.8; line-height: 1.6;">
                    Private • Secure • Fast • Free Forever
                </p>
            </div>

            <!-- Upload Card -->
            <div class="upload-card" style="
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
                position: relative;
                overflow: hidden;
            ">
                <!-- Background Pattern -->
                <div class="card-pattern" style="
                    position: absolute;
                    top: 0;
                    right: 0;
                    width: 150px;
                    height: 150px;
                    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
                    border-radius: 0 20px 0 120px;
                    z-index: 0;
                "></div>

                <div class="card-content" style="position: relative; z-index: 1;">
                    <!-- Queue Status -->
                    <div id="queueStatus" class="queue-status" style="
                        display: none;
                        background: linear-gradient(135deg, rgba(255, 243, 205, 0.9), rgba(255, 234, 167, 0.9));
                        border: 1px solid rgba(255, 193, 7, 0.3);
                        border-radius: 15px;
                        padding: 25px;
                        margin-bottom: 30px;
                        backdrop-filter: blur(10px);
                    ">
                        <div class="queue-header d-flex align-items-center mb-3">
                            <i class="fa-solid fa-clock me-3" style="color: #856404; font-size: 1.5rem;"></i>
                            <h5 style="color: #856404; margin: 0; font-weight: 600;">Upload Queue</h5>
                        </div>
                        <div id="queueMessage" style="color: #856404; font-size: 1rem; margin-bottom: 15px;">
                            Please wait for your turn...
                        </div>
                        <div class="progress" style="height: 12px; border-radius: 10px; background: rgba(133, 100, 4, 0.2);">
                            <div id="queueProgress" class="progress-bar progress-bar-striped progress-bar-animated"
                                style="width: 0%; background: linear-gradient(135deg, #ffc107, #ffb300); border-radius: 10px;">
                            </div>
                        </div>
                        <small id="queueDetails" class="text-muted mt-2 d-block" style="color: #856404 !important;">
                            Checking queue status...
                        </small>
                    </div>

                    <!-- Upload Form -->
                    <div id="uploadForm">
                        <div class="file-input-container mb-4">
                            <input type="file" id="fileInput" multiple accept=".zip" required style="display: none;">
                            <label for="fileInput" class="custom-file-label" id="fileLabel" style="
                                display: block;
                                background: rgba(255, 255, 255, 0.1);
                                border: 2px dashed rgba(255, 255, 255, 0.3);
                                border-radius: 15px;
                                padding: 40px 20px;
                                text-align: center;
                                color: #fff;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                font-size: 1.1rem;
                                font-weight: 500;
                                margin-left:40px;
                            ">
                                <div class="label-content">
                                    <i class="fa-solid fa-folder-open mb-3" style="font-size: 2.5rem; opacity: 0.7;"></i>
                                    <div class="label-text">📁 Choose files to upload</div>
                                    <div class="label-subtext" style="font-size: 0.9rem; opacity: 0.6; margin-top: 8px;">
                                        Drag & drop or click to browse
                                    </div>
                                </div>
                            </label>
                        </div>

                        <button id="uploadBtn" class="btn btn-upload w-100" type="submit" style="
                            background: linear-gradient(135deg, #28a745, #20c997);
                            border: none;
                            color: white;
                            padding: 18px 30px;
                            border-radius: 15px;
                            font-size: 1.2rem;
                            font-weight: 600;
                            transition: all 0.3s ease;
                            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
                            position: relative;
                            overflow: hidden;
                        ">
                            <i class="fa-solid fa-rocket me-2"></i>
                            Start Upload
                            <div class="btn-shine" style="
                                position: absolute;
                                top: -50%;
                                left: -50%;
                                width: 200%;
                                height: 200%;
                                background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
                                transform: rotate(45deg);
                                transition: all 0.5s ease;
                            "></div>
                        </button>

                        <div class="upload-info text-center mt-3">
                            <small class="text-light" style="opacity: 0.7;">
                                <i class="fa-solid fa-shield-alt me-1"></i>
                                Max upload size: <b>2 GB</b> • Only ZIP files supported
                            </small>
                        </div>
                    </div>

                    <!-- Progress Section -->
                    <div id="progressSection" style="display: none; margin-top: 30px;">
                        <div class="progress-header d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-white mb-0" style="font-weight: 600;">
                                <i class="fa-solid fa-chart-line me-2"></i>
                                Upload Progress
                            </h5>
                            <span id="fileCounter" class="badge" style="
                                background: rgba(102, 126, 234, 0.3);
                                color: #667eea;
                                padding: 8px 15px;
                                border-radius: 20px;
                                font-size: 0.9rem;
                            ">0/0</span>
                        </div>
                        
                        <div class="progress-container" style="
                            background: rgba(255, 255, 255, 0.1);
                            border-radius: 15px;
                            overflow: hidden;
                            height: 25px;
                            margin-bottom: 15px;
                            position: relative;
                        ">
                            <div id="combinedProgress" class="progress-bar" style="
                                height: 100%;
                                width: 0%;
                                background: linear-gradient(135deg, #28a745, #20c997);
                                color: white;
                                text-align: center;
                                font-weight: 600;
                                line-height: 25px;
                                font-size: 0.9rem;
                                transition: width 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            ">
                                <div class="progress-shine" style="
                                    position: absolute;
                                    top: 0;
                                    left: -100%;
                                    width: 100%;
                                    height: 100%;
                                    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
                                    animation: shine 2s infinite;
                                "></div>
                                0%
                            </div>
                        </div>
                        
                        <div class="progress-stats d-flex justify-content-between align-items-center">
                            <div id="combinedSpeed" class="text-light" style="font-size: 0.9rem; opacity: 0.8;">
                                <i class="fa-solid fa-gauge-high me-1"></i>
                                Speed: 0 KB/s
                            </div>
                            <div class="progress-percent text-light" style="font-size: 0.9rem; opacity: 0.8;">
                                <span id="percentText">0%</span> Complete
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="features-grid mt-5">
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-white">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-lock" style="
                                    font-size: 2rem;
                                    color: #28a745;
                                    background: rgba(40, 167, 69, 0.1);
                                    padding: 15px;
                                    border-radius: 15px;
                                "></i>
                            </div>
                            <h6 style="font-weight: 600;">Private Mode</h6>
                            <p style="opacity: 0.7; font-size: 0.9rem;">Your files are secure and private</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-white">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-bolt" style="
                                    font-size: 2rem;
                                    color: #ffc107;
                                    background: rgba(255, 193, 7, 0.1);
                                    padding: 15px;
                                    border-radius: 15px;
                                "></i>
                            </div>
                            <h6 style="font-weight: 600;">Fast Upload</h6>
                            <p style="opacity: 0.7; font-size: 0.9rem;">High-speed file processing</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-white">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-infinity" style="
                                    font-size: 2rem;
                                    color: #667eea;
                                    background: rgba(102, 126, 234, 0.1);
                                    padding: 15px;
                                    border-radius: 15px;
                                "></i>
                            </div>
                            <h6 style="font-weight: 600;">Free Forever</h6>
                            <p style="opacity: 0.7; font-size: 0.9rem;">No limits, no payments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const PUSHER_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
        const PUSHER_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

        let serverPercent = 0;
        let displayedSpeed = { value: 0 };
        //const serverWeight = Math.random() * 0.2 + 0.7;
        const serverWeight = Math.random() * 0.2 + 0.4;
        const browserWeight = 1 - serverWeight;

        const pusher = new Pusher(PUSHER_KEY, {
            cluster: PUSHER_CLUSTER,
            forceTLS: true
        });
        const channel = pusher.subscribe('upload-progress');
        channel.bind('progress-updated', function(data) {
            serverPercent = data.percent;
            smoothSpeedDisplay(data.speed * 1024, 'KB/s');
            updateCombinedProgress();
        });

        let currentFileIndex = 0;
        let totalFiles = 0;
        let browserPercent = 0;
        let isInQueue = false;
        let queueCheckInterval = null;

        // File input label update
        const fileInput = document.getElementById('fileInput');
        const fileLabel = document.getElementById('fileLabel');

        fileInput.addEventListener('change', () => {
            const files = fileInput.files;
            if (files.length === 0) {
                fileLabel.innerHTML = `
                    <div class="label-content">
                        <i class="fa-solid fa-folder-open mb-3" style="font-size: 2.5rem; opacity: 0.7;"></i>
                        <div class="label-text">📁 Choose files to upload</div>
                        <div class="label-subtext" style="font-size: 0.9rem; opacity: 0.6; margin-top: 8px;">
                            Drag & drop or click to browse
                        </div>
                    </div>
                `;
            } else if (files.length === 1) {
                fileLabel.innerHTML = `
                    <div class="label-content">
                        <i class="fa-solid fa-file-zipper mb-3" style="font-size: 2.5rem; color: #28a745;"></i>
                        <div class="label-text">${files[0].name}</div>
                        <div class="label-subtext" style="font-size: 0.9rem; opacity: 0.6; margin-top: 8px;">
                            1 file selected • ${(files[0].size / 1024 / 1024).toFixed(2)} MB
                        </div>
                    </div>
                `;
            } else {
                const totalSize = Array.from(files).reduce((acc, file) => acc + file.size, 0);
                fileLabel.innerHTML = `
                    <div class="label-content">
                        <i class="fa-solid fa-files mb-3" style="font-size: 2.5rem; color: #667eea;"></i>
                        <div class="label-text">${files.length} files selected</div>
                        <div class="label-subtext" style="font-size: 0.9rem; opacity: 0.6; margin-top: 8px;">
                            ${(totalSize / 1024 / 1024).toFixed(2)} MB total
                        </div>
                    </div>
                `;
            }
        });

        // Drag and drop functionality
        fileLabel.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileLabel.style.background = 'rgba(102, 126, 234, 0.2)';
            fileLabel.style.borderColor = '#667eea';
        });

        fileLabel.addEventListener('dragleave', (e) => {
            e.preventDefault();
            fileLabel.style.background = 'rgba(255, 255, 255, 0.1)';
            fileLabel.style.borderColor = 'rgba(255, 255, 255, 0.3)';
        });

        fileLabel.addEventListener('drop', (e) => {
            e.preventDefault();
            fileLabel.style.background = 'rgba(255, 255, 255, 0.1)';
            fileLabel.style.borderColor = 'rgba(255, 255, 255, 0.3)';
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        });

        function updateCombinedProgress() {
            let combinedPercent = browserPercent * browserWeight + serverPercent * serverWeight;
            combinedPercent = Math.min(100, Math.round(combinedPercent));
            const progressBar = document.getElementById('combinedProgress');
            progressBar.style.width = combinedPercent + '%';
            progressBar.innerHTML = `<div class="progress-shine"></div>${combinedPercent}%`;
            document.getElementById('percentText').textContent = combinedPercent + '%';
            document.getElementById('fileCounter').textContent = (currentFileIndex + 1) + '/' + totalFiles;
        }

        function smoothSpeedDisplay(target, unit = 'KB/s') {
            const animate = () => {
                displayedSpeed.value += (target - displayedSpeed.value) * 0.1;
                document.getElementById('combinedSpeed').innerHTML = 
                    `<i class="fa-solid fa-gauge-high me-1"></i>Speed: ${displayedSpeed.value.toFixed(2)} ${unit}`;
                if (Math.abs(displayedSpeed.value - target) > 0.01) requestAnimationFrame(animate);
            };
            animate();
        }

        function showQueueStatus(position, current, limit) {
            isInQueue = true;
            document.getElementById('queueStatus').style.display = 'block';
            document.getElementById('uploadForm').style.opacity = '0.6';
            document.getElementById('uploadBtn').disabled = true;

            const progressPercent = Math.min(100, ((position - 1) / limit) * 100);
            document.getElementById('queueProgress').style.width = progressPercent + '%';
            document.getElementById('queueMessage').innerHTML =
                `<strong>Queue Position: ${position}</strong> - Please wait for your turn...`;
            document.getElementById('queueDetails').innerText = `${current} active users / ${limit} slots available`;
        }

        function hideQueueStatus() {
            isInQueue = false;
            document.getElementById('queueStatus').style.display = 'none';
            document.getElementById('uploadForm').style.opacity = '1';
            document.getElementById('uploadBtn').disabled = false;

            if (queueCheckInterval) {
                clearInterval(queueCheckInterval);
                queueCheckInterval = null;
            }
        }

        async function checkUploadQueueAndProceed(files) {
            try {
                const rateLimitResponse = await axios.post('/upload/check-rate-limit', {}, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                if (rateLimitResponse.data.allowed) {
                    hideQueueStatus();
                    await uploadFilesSequentially(files);
                }

            } catch (error) {
                if (error.response && error.response.data.error === 'rate_limit_exceeded') {
                    const data = error.response.data;
                    showQueueStatus(data.queue_position, data.current_users, data.limit);

                    if (!queueCheckInterval) {
                        queueCheckInterval = setInterval(() => {
                            checkUploadQueueAndProceed(files);
                        }, 5000);
                    }
                } else {
                    hideQueueStatus();
                    let errorMessage = 'Upload failed: ';
                    if (error.response?.data?.errors?.file) {
                        errorMessage += error.response.data.errors.file[0];
                    } else if (error.response?.data?.message) {
                        errorMessage += error.response.data.message;
                    } else if (error.response?.data?.error) {
                        errorMessage += error.response.data.error;
                    } else {
                        errorMessage += error.message;
                    }

                    alert(errorMessage);
                }
            }
        }

        async function uploadFilesSequentially(files) {
            document.getElementById('progressSection').style.display = 'block';

            const uploadedSlugs = [];
            totalFiles = files.length;
            currentFileIndex = 0;

            for (let i = 0; i < files.length; i++) {
                currentFileIndex = i;
                const file = files[i];
                browserPercent = 0;
                serverPercent = 0;

                const formData = new FormData();
                formData.append('file', file);

                const startTime = Date.now();

                try {
                    const res = await axios.post('/upload', formData, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function(progressEvent) {
                            browserPercent = (progressEvent.loaded / progressEvent.total) * 100;
                            browserPercent = Math.min(100, browserPercent);
                            const elapsed = (Date.now() - startTime) / 1000;
                            const speedKBps = (progressEvent.loaded / 1024) / elapsed;
                            smoothSpeedDisplay(speedKBps, 'KB/s');
                            updateCombinedProgress();
                        }
                    });

                    uploadedSlugs.push(...res.data.uploaded_slugs);

                } catch (err) {
                    console.error(err);
                    alert('Upload failed for file: ' + file.name);
                }
            }

            if (uploadedSlugs.length) {
                const slugList = uploadedSlugs.join(',');
                window.location.href = '/download/' + encodeURIComponent(slugList);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('uploadBtn').addEventListener('click', () => {
                if (!fileInput.files.length) return alert('Select at least one ZIP file!');
                checkUploadQueueAndProceed(fileInput.files);
            });

            // Add hover effects
            const uploadBtn = document.getElementById('uploadBtn');
            uploadBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 12px 30px rgba(40, 167, 69, 0.4)';
            });
            
            uploadBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 8px 25px rgba(40, 167, 69, 0.3)';
            });

            fileLabel.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255, 255, 255, 0.15)';
                this.style.borderColor = 'rgba(255, 255, 255, 0.4)';
            });
            
            fileLabel.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 255, 255, 0.1)';
                this.style.borderColor = 'rgba(255, 255, 255, 0.3)';
            });
        });
    </script>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }

        .btn-upload:hover .btn-shine {
            transform: rotate(45deg) translateX(100%);
        }

        body {
            background: #151820;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .upload-card {
            transition: all 0.3s ease;
        }

        .upload-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4) !important;
        }
    </style>
@endsection