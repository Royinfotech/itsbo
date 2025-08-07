<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student QR Code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: url('../pictures/LoginBackg.png') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .qr-container {
            text-align: center;
            padding: 10px;
            max-width: 800px;
            max-height: 800px;
            margin: 25px auto;
            background: white;
            border-radius: 12px;
            border-top: #600000 8px solid;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .student-info {
            margin: 20px 0;
            padding: 15px;
            background: #f8f8f8;
            border-radius: 8px;
        }
        .student-info h2 {
            color: maroon;
            margin-bottom: 15px;
        }
        .qr-code {
            margin: 30px auto;
            padding: 20px;
            background: white;
            border: 2px solid maroon;
            border-radius: 8px;
            width: fit-content;
        }
        .qr-code svg {
            width: 300px;
            height: 300px;
        }
        .error-message {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .refresh-btn {
            background: maroon;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
            transition: background-color 0.3s;
        }
        .refresh-btn:hover {
            background: #600000;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .download-btn {
            background: #006400;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .download-btn:hover {
            background: #004d00;
        }
        .download-btn i {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="student-info">
            <h2>Student QR Code</h2>
            <p><strong>ID:</strong> {{ $student->student_id }}</p>
            <p><strong>Name:</strong> {{ $student->student_name }}</p>
        </div>

        @if(isset($error))
            <div class="error-message">{{ $error }}</div>
        @endif

        @if(isset($qrcode))
            <div class="qr-code" id="qrcode">
                {!! $qrcode !!}
            </div>
            <div class="button-container">
                <button class="refresh-btn" onclick="location.reload()">
                    <i class="fas fa-sync"></i> Refresh QR Code
                </button>
                <button class="download-btn" onclick="downloadQR()">
                    <i class="fas fa-download"></i> Download QR Code
                </button>
            </div>
        @endif
    </div>

    <script>
        function downloadQR() {
            // Get the SVG element
            const svg = document.querySelector('.qr-code svg');
            
            // Create a canvas element
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // Set canvas size (make it larger for better quality)
            canvas.width = 1024;
            canvas.height = 1024;
            
            // Create an image element
            const img = new Image();
            
            // Convert SVG to data URL
            const svgData = new XMLSerializer().serializeToString(svg);
            const svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
            const url = URL.createObjectURL(svgBlob);
            
            img.onload = function() {
                // Fill white background
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                // Draw the image
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                
                // Convert to PNG and download
                const pngUrl = canvas.toDataURL('image/png');
                const downloadLink = document.createElement('a');
                downloadLink.href = pngUrl;
                downloadLink.download = 'qrcode_{{ $student->student_id }}.png';
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
                URL.revokeObjectURL(url);
            };
            
            img.src = url;
        }
    </script>
</body>
</html>