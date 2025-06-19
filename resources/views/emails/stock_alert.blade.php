<!DOCTYPE html>
<html>
<head>
    <title>Peringatan Stok Rendah!</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e0e0e0;
        }
        h1 {
            color: #d9534f;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        strong {
            color: #555555;
        }
        .highlight {
            font-size: 18px;
            font-weight: bold;
            color: #d9534f;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #888888;
            border-top: 1px solid #eeeeee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Peringatan Stok Rendah! <span style="font-size: 20px;">&#9888;</span></h1>
    <p>Halo Tim,</p>
    <p>Produk berikut ini memiliki stok yang rendah dan memerlukan perhatian segera:</p>
    <p>Nama Produk: <strong class="highlight">{{ $product->name }}</strong></p>
    <p>Stok Saat Ini: <strong class="highlight">{{ $product->stock }}</strong></p>
    <p>Batas Stok Minimum (Alert): <strong class="highlight">{{ $product->stock_alert }}</strong></p>
    <p>Mohon segera lakukan pengecekan dan tindakan yang diperlukan untuk produk ini.</p>
    <div class="footer">
        Ini adalah pesan otomatis. Mohon jangan membalas email ini.
        <br>
        &copy; {{ date('Y') }} Sistem Manajemen Inventaris Anda.
    </div>
</div>
</body>
</html>
