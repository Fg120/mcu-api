<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCU API Test Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5em;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .card p {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .response-box {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }

        .response-box h3 {
            color: #667eea;
            margin-bottom: 15px;
        }

        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            max-height: 400px;
            overflow-y: auto;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .status-success {
            color: #28a745;
            font-weight: bold;
        }

        .status-error {
            color: #dc3545;
            font-weight: bold;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .info-box strong {
            color: #1976D2;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🧪 MCU API Test Panel</h1>

        <div class="grid">
            <!-- Sync Lab Card -->
            <div class="card">
                <h2>📋 Sync Lab Data</h2>
                <p>Sinkronisasi data laboratorium dengan kategori dan layanan ke external API.</p>
                <div class="info-box">
                    <strong>Endpoint:</strong> /api/sync_lab<br>
                    <strong>Method:</strong> POST
                </div>
                <button class="btn" onclick="testSyncLab(this)">
                    Test Sync Lab
                </button>
            </div>

            <!-- Send Single Hasil Lab Card (No File) -->
            <div class="card">
                <h2>📄 Send Hasil Lab (No File)</h2>
                <p>Mengirim hasil laboratorium single pasien tanpa file PDF.</p>
                <div class="info-box">
                    <strong>Endpoint:</strong> /api/hasil_lab<br>
                    <strong>Method:</strong> POST<br>
                    <strong>File:</strong> Tidak ada
                </div>
                <button class="btn" onclick="testSendHasilLab(this)">
                    Test Send (No File)
                </button>
            </div>

            <!-- Send Single Hasil Lab Card (With File) -->
            <div class="card">
                <h2>📎 Send Hasil Lab (With File)</h2>
                <p>Mengirim hasil laboratorium single pasien dengan file PDF (base64).</p>
                <div class="info-box">
                    <strong>Endpoint:</strong> /api/hasil_lab<br>
                    <strong>Method:</strong> POST<br>
                    <strong>File:</strong> dokumen.pdf
                </div>
                <button class="btn" disabled onclick="testSendHasilLabWithFile(this)">
                    Test Send (With File)
                </button>
            </div>

            <!-- Send Multiple Hasil Lab Card -->
            <div class="card">
                <h2>📊 Send Hasil Lab (Multiple)</h2>
                <p>Data dikirimkan satu per satu ke Endpoint.</p>
                <div class="info-box">
                    <strong>Endpoint:</strong> /api/hasil_lab (batch)<br>
                    <strong>Method:</strong> POST<br>
                    <strong>Count:</strong> 2 pasien
                </div>
                <button class="btn" onclick="testSendMultipleHasilLab(this)">
                    Test Send Multiple
                </button>
            </div>

            <div class="card">
                <h2>📊 Send Hasil Lab (Multiple Bulk)</h2>
                <p>Data dikirimkan sekaligus berupa array ke Endpoint, dan di proses satu per satu di Endpoint.</p>
                <div class="info-box">
                    <strong>Endpoint:</strong> /api/hasil_lab/bulk (batch)<br>
                    <strong>Method:</strong> POST<br>
                    <strong>Count:</strong> 2 pasien
                </div>
                <button class="btn" onclick="testSendMultipleHasilLabBulk(this)">
                    Test Send Multiple
                </button>
            </div>
        </div>

        <!-- Response Box -->
        <div class="response-box" id="responseBox" style="display: none;">
            <h3>Response</h3>
            <div id="responseContent"></div>
        </div>
    </div>

    <script>
        async function testSyncLab(btn) {
            showLoading(btn);
            try {
                const response = await fetch('/test/sync-lab', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                showResponse(data, response.ok);
            } catch (error) {
                showResponse({
                    error: error.message
                }, false);
            } finally {
                hideLoading(btn, 'Test Sync Lab');
            }
        }

        async function testSendHasilLab(btn) {
            showLoading(btn);
            try {
                const response = await fetch('/test/send-hasil-lab', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                showResponse(data, response.ok);
            } catch (error) {
                showResponse({
                    error: error.message
                }, false);
            } finally {
                hideLoading(btn, 'Test Send (No File)');
            }
        }

        async function testSendHasilLabWithFile(btn) {
            // showLoading(btn);
            // try {
            //     const response = await fetch('/test/send-hasil-lab-with-file', {
            //         method: 'GET',
            //         headers: {
            //             'Accept': 'application/json',
            //         }
            //     });
            //     const data = await response.json();
            //     showResponse(data, response.ok);
            // } catch (error) {
            //     showResponse({
            //         error: error.message
            //     }, false);
            // } finally {
            //     hideLoading(btn, 'Test Send (With File)');
            // }
        }

        async function testSendMultipleHasilLab(btn) {
            showLoading(btn);
            try {
                const response = await fetch('/test/send-multiple-hasil-lab', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                showResponse(data, response.ok);
            } catch (error) {
                showResponse({
                    error: error.message
                }, false);
            } finally {
                hideLoading(btn, 'Test Send Multiple');
            }
        }

        async function testSendMultipleHasilLabBulk(btn) {
            showLoading(btn);
            try {
                const response = await fetch('/test/send-multiple-hasil-lab-bulk', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                showResponse(data, response.ok);
            } catch (error) {
                showResponse({
                    error: error.message
                }, false);
            } finally {
                hideLoading(btn, 'Test Send Multiple');
            }
        }

        function showLoading(btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="loading"></span> Loading...';
        }

        function hideLoading(btn, text) {
            btn.disabled = false;
            btn.innerHTML = text;
        }

        function showResponse(data, success) {
            const box = document.getElementById('responseBox');
            const content = document.getElementById('responseContent');

            const statusClass = success ? 'status-success' : 'status-error';
            const statusText = success ? '✅ SUCCESS' : '❌ ERROR';

            content.innerHTML = `
                <p class="${statusClass}">${statusText}</p>
                <pre>${JSON.stringify(data, null, 2)}</pre>
            `;

            box.style.display = 'block';
            box.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    </script>
</body>

</html>
