<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>413 - Ukuran File Terlalu Besar | SIM-PBL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #F6F4F1;
            color: #1F1F1F;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            box-sizing: border-box;
        }
        .error-card {
            background: #FFFFFF;
            border: 1px solid #CFC7BC;
            border-radius: 16px;
            padding: 40px 32px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .error-icon {
            width: 56px;
            height: 56px;
            background-color: #FFF7ED;
            color: #C2410C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
        }
        h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #1F1F1F;
        }
        p {
            font-size: 13px;
            color: #7D746D;
            line-height: 1.6;
            margin: 0 0 24px;
        }
        .btn-back {
            display: inline-block;
            background-color: #7A5C4A;
            color: #FFFFFF;
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .btn-back:hover {
            background-color: #634737;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">⚠</div>
        <h1>Ukuran File Terlalu Besar</h1>
        <p>File yang Anda unggah melebihi batas kapasitas maksimal yang ditentukan (<strong>2 MB</strong>). Silakan kembali dan pilih file dengan ukuran lebih kecil.</p>
        <a href="javascript:history.back()" class="btn-back">Kembali ke Halaman Sebelumnya</a>
    </div>
</body>
</html>
