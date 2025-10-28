@extends('users.layouts.master')
@section('title', 'Multi Upload')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="hosting" style="margin: 40px auto; max-width: 600px;">
        <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">
        <div class="upload-card">

            <h3>The free-for-all File Hosting.</h3>
            <p>Running in private mode.</p>
            <div id="uploadForm">
                <label for="fileInput" class="custom-file-label" id="fileLabel">📁 Choose a file to upload</label>
                <input type="file" id="fileInput" name="file" multiple accept=".zip" required>
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
                        0%</div>
                </div>
                <div class="mt-2" id="combinedSpeed" style="font-size:14px;">Speed: 0 MB/s</div>
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
        const serverWeight = 0.5;
        const browserWeight = 1 - serverWeight;

        const pusher = new Pusher(PUSHER_KEY, {
            cluster: PUSHER_CLUSTER,
            forceTLS: true
        });
        const channel = pusher.subscribe('upload-progress');
        channel.bind('progress-updated', function(data) {
            serverPercent = data.percent;
            smoothSpeedDisplay(data.speed);
            updateCombinedProgress();
        });

        let currentFileIndex = 0;
        let totalFiles = 0;
        let browserPercent = 0;

        function updateCombinedProgress() {
            let combinedPercent = browserPercent * browserWeight + serverPercent * serverWeight;
            combinedPercent = Math.min(100, Math.round(combinedPercent));
            document.getElementById('combinedProgress').style.width = combinedPercent + '%';
            document.getElementById('combinedProgress').innerText = combinedPercent + '% (' + (currentFileIndex + 1) + '/' +
                totalFiles + ')';
        }

        function smoothSpeedDisplay(target) {
            const animate = () => {
                displayedSpeed.value += (target - displayedSpeed.value) * 0.1;
                document.getElementById('combinedSpeed').innerText = 'Speed: ' + displayedSpeed.value.toFixed(2) +
                    ' MB/s';
                if (Math.abs(displayedSpeed.value - target) > 0.01) requestAnimationFrame(animate);
            };
            animate();
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
                            const speed = progressEvent.loaded / 1024 / 1024 / elapsed;
                            smoothSpeedDisplay(speed);
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
                const fileInput = document.getElementById('fileInput');
                if (!fileInput.files.length) return alert('Select at least one ZIP file!');
                uploadFilesSequentially(fileInput.files);
            });
        });
    </script>
@endsection
