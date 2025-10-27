<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>1Fichier Multi Upload</title>
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
body { font-family: sans-serif; margin: 40px; }
.progress-container { width: 100%; background: #eee; border-radius: 6px; overflow: hidden; margin-bottom: 20px; }
.progress-bar { height: 25px; width: 0%; background: #4CAF50; text-align: center; color: white; font-size: 14px; transition: width 0.2s ease; }
.speed { margin-top: 5px; font-size: 14px; }
</style>
<script>
const PUSHER_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

let browserPercent = 0;
let serverPercent = 0;
let displayedSpeed = { value: 0 };
const browserWeight = Math.random() * 0.1 + 0.45; // 0.45-0.55
const serverWeight = 1 - browserWeight;

function updateCombinedProgress() {
    let combinedPercent = browserPercent * browserWeight + serverPercent * serverWeight;
    combinedPercent = Math.min(100, Math.round(combinedPercent)); // ✅ Cap at 100%
    document.getElementById('combinedProgress').style.width = combinedPercent + '%';
    document.getElementById('combinedProgress').innerText = combinedPercent + '%';
}

function smoothSpeedDisplay(target) {
    const animate = () => {
        displayedSpeed.value += (target - displayedSpeed.value) * 0.1;
        document.getElementById('combinedSpeed').innerText = 'Speed: ' + displayedSpeed.value.toFixed(2) + ' MB/s';
        if (Math.abs(displayedSpeed.value - target) > 0.01) requestAnimationFrame(animate);
    };
    animate();
}

// ✅ Pusher for server progress
const pusher = new Pusher(PUSHER_KEY, { cluster: PUSHER_CLUSTER, forceTLS: true });
const channel = pusher.subscribe('upload-progress');
channel.bind('progress-updated', function(data) {
    serverPercent = data.percent;
    smoothSpeedDisplay(data.speed);
    updateCombinedProgress();
});

document.addEventListener('DOMContentLoaded', () => {
    const uploadBtn = document.getElementById('uploadBtn');
    uploadBtn.addEventListener('click', async () => {
        const fileInput = document.getElementById('fileInput');
        if (!fileInput.files.length) return alert('Select at least one ZIP file!');

        const formData = new FormData();
        for (let file of fileInput.files) formData.append('file[]', file);

        const startTime = Date.now();

        try {
            const res = await axios.post('/upload', formData, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: function(progressEvent) {
                    browserPercent = (progressEvent.loaded / progressEvent.total) * 100;
                    browserPercent = Math.min(100, browserPercent); // ✅ Cap at 100%
                    const elapsed = (Date.now() - startTime) / 1000;
                    const speed = progressEvent.loaded / 1024 / 1024 / elapsed;
                    smoothSpeedDisplay(speed);
                    updateCombinedProgress();
                }
            });

            if (res.data.uploaded_slugs.length) {
                const slugList = res.data.uploaded_slugs.join(',');
                window.location.href = '/download/' + encodeURIComponent(slugList);
            }

        } catch (err) {
            console.error(err);
            alert('Upload failed!');
        }
    });
});
</script>
</head>
<body>
<h2>📤 Upload ZIP Files (Max 2GB Total)</h2>
<input type="file" id="fileInput" multiple accept=".zip">
<button id="uploadBtn">Upload</button>

<h3>Upload Progress</h3>
<div class="progress-container">
    <div id="combinedProgress" class="progress-bar">0%</div>
</div>
<div class="speed" id="combinedSpeed">Speed: 0 MB/s</div>
</body>
</html>
