<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hCaptcha Test - Laravel</title>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        
        .h-captcha {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        
        button {
            background: #6366f1;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
        }
        
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h2>hCaptcha Test - Laravel</h2>
        <p><strong>Site Key:</strong> {{ env('HCAPTCHA_SITE_KEY') }}</p>

        <form id="captchaTestForm">
            <div class="h-captcha" data-sitekey="{{ env('HCAPTCHA_SITE_KEY') }}"></div>
            
            <button type="button" onclick="testCaptcha()" id="testBtn">
                Test hCaptcha
            </button>
        </form>

        <div id="result" class="result" style="display:none;"></div>
    </div>

    <script>
        function testCaptcha() {
            const hcaptchaResponse = document.querySelector('[name="h-captcha-response"]');
            const resultDiv = document.getElementById('result');
            const testBtn = document.getElementById('testBtn');

            if (!hcaptchaResponse || !hcaptchaResponse.value) {
                showResult('Please complete the hCaptcha first!', 'error');
                return;
            }

            testBtn.disabled = true;
            testBtn.innerHTML = 'Testing...';

            // Send to Laravel backend for verification
            fetch('/verify-test-captcha', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    'h-captcha-response': hcaptchaResponse.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult('✅ hCaptcha verification SUCCESSFUL!', 'success');
                } else {
                    showResult('❌ hCaptcha verification FAILED: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showResult('❌ Network error: ' + error.message, 'error');
            })
            .finally(() => {
                testBtn.disabled = false;
                testBtn.innerHTML = 'Test hCaptcha Again';
                
                if (typeof hcaptcha !== 'undefined') {
                    hcaptcha.reset();
                }
            });
        }

        function showResult(message, type) {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = message;
            resultDiv.className = `result ${type}`;
            resultDiv.style.display = 'block';
        }
    </script>
</body>
</html>