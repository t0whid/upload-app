@extends('users.layouts.master')
@section('title', 'Multi Upload')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="hosting" style="margin: 40px auto; max-width: 600px;">
        <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">
        <div class="upload-card">

            <h3>The free-for-all File Hosting.</h3>
            <p>Running in private mode.</p>

            <!-- Queue Status (hidden initially) -->
            <div id="queueStatus"
                style="display:none; background:#fff3cd; border:1px solid #ffeaa7; border-radius:6px; padding:15px; margin-bottom:20px;">
                <h5><i class="fa-solid fa-clock"></i> Upload Queue</h5>
                <div id="queueMessage">Please wait for your turn...</div>
                <div class="progress mt-2" style="height:10px;">
                    <div id="queueProgress" class="progress-bar progress-bar-striped progress-bar-animated"
                        style="width:0%"></div>
                </div>
                <small id="queueDetails" class="text-muted"></small>
            </div>

            <div id="uploadForm">
                <label for="fileInput" class="custom-file-label" id="fileLabel">📁 Choose a file to upload</label>
                <input type="file" id="fileInput" multiple accept=".zip" required>
                <button id="uploadBtn" class="btn btn-purple" type="submit">🚀 Upload</button>
                <div class="small-text">Max upload size: <b>2 GB (only zip)</b></div>
            </div>

            <!-- Progress section (hidden initially) -->
            <div id="progressSection" style="display:none; margin-top:30px;">
                <h4>Upload Progress</h4>
                <div class="progress-container"
                    style="width:100%; background:#eee; border-radius:6px; overflow:hidden; height:25px;">
                    <div id="combinedProgress" class="progress-bar"
                        style="height:100%; width:0%; background:#4CAF50; color:#fff; text-align:center; font-weight:bold; line-height:25px;">
                        0%
                    </div>
                </div>
                <div class="mt-2" id="combinedSpeed" style="font-size:14px;">Speed: 0 KB/s</div>
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
        let displayedSpeed = {
            value: 0
        };
        const serverWeight = Math.random() * 0.2 + 0.7;
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
                fileLabel.textContent = '📁 Choose a file to upload';
            } else if (files.length === 1) {
                fileLabel.textContent = `📁 1 file selected: ${files[0].name}`;
            } else {
                const maxShow = 5;
                const fileNames = Array.from(files).slice(0, maxShow).map(f => f.name).join(', ');
                const moreText = files.length > maxShow ? `... (+${files.length - maxShow} more)` : '';
                fileLabel.textContent = `📁 ${files.length} files selected: ${fileNames} ${moreText}`;
            }
        });

        function updateCombinedProgress() {
            let combinedPercent = browserPercent * browserWeight + serverPercent * serverWeight;
            combinedPercent = Math.min(100, Math.round(combinedPercent));
            document.getElementById('combinedProgress').style.width = combinedPercent + '%';
            document.getElementById('combinedProgress').innerText = combinedPercent + '% (' + (currentFileIndex + 1) + '/' +
                totalFiles + ')';
        }

        function smoothSpeedDisplay(target, unit = 'KB/s') {
            const animate = () => {
                displayedSpeed.value += (target - displayedSpeed.value) * 0.1;
                document.getElementById('combinedSpeed').innerText = 'Speed: ' + displayedSpeed.value.toFixed(2) + ' ' +
                    unit;
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
                // OPTION 1: Use separate rate limit check endpoint (Recommended)
                // First check if separate endpoint exists
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

                    // Auto-retry every 5 seconds
                    if (!queueCheckInterval) {
                        queueCheckInterval = setInterval(() => {
                            checkUploadQueueAndProceed(files);
                        }, 5000);
                    }
                } else {
                    hideQueueStatus();

                    // Show proper error message
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
        });
    </script>
@endsection
