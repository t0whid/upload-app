<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>1Fichier Upload</title>
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
    body { font-family: sans-serif; margin: 40px; }
    .progress-container { width: 100%; background: #eee; border-radius: 6px; overflow: hidden; margin-bottom: 20px; }
    .progress-bar { height: 25px; width: 0%; background: #4CAF50; text-align: center; color: white; font-size: 14px; transition: width 0.3s ease; }
    .speed { margin-top: 5px; font-size: 14px; }
</style>
<script>
const PUSHER_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

let browserPercent = 0;
let serverPercent = 0;
let displayedSpeed = { value: 0 };
const browserWeight = Math.random() * 0.1 + 0.45; // 0.45–0.55
const serverWeight = 1 - browserWeight;

function updateCombinedProgress() {
    const combinedPercent = Math.round(browserPercent * browserWeight + serverPercent * serverWeight);
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

// Setup Pusher
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
        if (!fileInput.files.length) return alert('Select a file first!');
        const file = fileInput.files[0];

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
                    const elapsed = (Date.now() - startTime) / 1000;
                    const speed = progressEvent.loaded / 1024 / 1024 / elapsed;
                    smoothSpeedDisplay(speed);
                    updateCombinedProgress();
                }
            });

            updateCombinedProgress();
            smoothSpeedDisplay(0);

            // Display download links
            const linksDiv = document.getElementById('downloadLinks');
            linksDiv.innerHTML = '';
            if (res.data.links && res.data.links.length) {
                res.data.links.forEach(link => {
                    const a = document.createElement('a');
                    a.href = link.download;
                    a.target = '_blank';
                    a.innerText = link.filename + ' (' + (link.size/1024).toFixed(2) + ' KB)';
                    linksDiv.appendChild(a);
                    linksDiv.appendChild(document.createElement('br'));
                });
            } else {
                linksDiv.innerText = 'Upload completed, but no links returned.';
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
<h2>📤 Upload File to 1Fichier</h2>
<input type="file" id="fileInput">
<button id="uploadBtn">Upload</button>

<h3>Upload Progress</h3>
<div class="progress-container">
    <div id="combinedProgress" class="progress-bar">0%</div>
</div>
<div class="speed" id="combinedSpeed">Speed: 0 MB/s</div>

<h3>Download Links</h3>
<div id="downloadLinks"></div>
</body>
</html>
