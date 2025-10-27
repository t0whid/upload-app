<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>1Fichier Upload</title>
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .progress-container { width: 100%; background: #eee; border-radius: 6px; overflow: hidden; margin-bottom: 20px; }
        .progress-bar { height: 20px; width: 0%; background: #4CAF50; text-align: center; color: white; font-size: 12px; transition: width 0.3s ease; }
        .speed { margin-top: 5px; font-size: 14px; }
    </style>
</head>
<body>
    <h2>📤 Upload File to 1Fichier</h2>

    <input type="file" id="fileInput">
    <button id="uploadBtn">Upload</button>

    <h3>Browser → Laravel</h3>
    <div class="progress-container"><div id="browserProgress" class="progress-bar">0%</div></div>
    <div class="speed" id="browserSpeed">Speed: 0 MB/s</div>

    <h3>Laravel → 1Fichier</h3>
    <div class="progress-container"><div id="serverProgress" class="progress-bar">0%</div></div>
    <div class="speed" id="serverSpeed">Speed: 0 MB/s</div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // ✅ Setup Pusher
        const pusher = new Pusher('fc5e5e1c6156890eceab', {
            cluster: 'ap2',
            forceTLS: true
        });

        const channel = pusher.subscribe('upload-progress');
        channel.bind('progress-updated', function(data) {
            console.log("Server Progress:", data);
            document.getElementById('serverProgress').style.width = data.percent + '%';
            document.getElementById('serverProgress').innerText = data.percent + '%';
            document.getElementById('serverSpeed').innerText = 'Speed: ' + data.speed + ' MB/s';
        });

        // ✅ Upload Handler
        document.getElementById('uploadBtn').addEventListener('click', async () => {
            const fileInput = document.getElementById('fileInput');
            if (!fileInput.files.length) return alert('Select a file first!');
            const file = fileInput.files[0];

            const formData = new FormData();
            formData.append('file', file);

            const startTime = Date.now();

            try {
                const response = await axios.post('/upload', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function (progressEvent) {
                        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        const elapsed = (Date.now() - startTime) / 1000;
                        const speed = (progressEvent.loaded / 1024 / 1024 / elapsed).toFixed(2);

                        const bar = document.getElementById('browserProgress');
                        bar.style.width = percentCompleted + '%';
                        bar.innerText = percentCompleted + '%';
                        document.getElementById('browserSpeed').innerText = 'Speed: ' + speed + ' MB/s';
                    }
                });

                alert('Upload complete!');
            } catch (error) {
                console.error(error);
                alert('Upload failed!');
            }
        });
    </script>
</body>
</html>
