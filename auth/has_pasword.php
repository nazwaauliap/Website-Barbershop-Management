<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator - NZ Barbershop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .generator-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .output-box {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-family: monospace;
        }
        .btn-generate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-copy {
            background: #28a745;
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
        }
        h2 {
            color: #764ba2;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="generator-card">
        <h2><i class="bi bi-key-fill me-2"></i>Password Hash Generator</h2>
        <p class="text-muted">Tool untuk generate hash password yang aman untuk database pegawai NZ Barbershop</p>
        
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label class="form-label"><strong>Masukkan Password:</strong></label>
                <input type="text" name="password" class="form-control form-control-lg" placeholder="Contoh: admin123" required autofocus>
                <small class="text-muted">Password ini yang akan di-hash untuk disimpan di database</small>
            </div>
            <button type="submit" class="btn btn-generate w-100">
                <i class="bi bi-shield-lock me-2"></i>Generate Hash
            </button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['password'])) {
            $password = $_POST['password'];
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            echo '<div class="mt-4">';
            echo '<h5 class="text-success"><i class="bi bi-check-circle-fill me-2"></i>Hash Berhasil Dibuat!</h5>';
            
            echo '<div class="alert alert-info mt-3">';
            echo '<strong>Password Original:</strong><br>';
            echo '<code style="font-size: 16px;">' . htmlspecialchars($password) . '</code>';
            echo '</div>';
            
            echo '<div class="alert alert-warning">';
            echo '<strong>Password Hash (Copy ini ke database):</strong><br>';
            echo '<div class="output-box" id="hashOutput">' . htmlspecialchars($hash) . '</div>';
            echo '<button class="btn btn-copy" onclick="copyHash()"><i class="bi bi-clipboard me-1"></i>Copy Hash</button>';
            echo '</div>';
            
            echo '<div class="alert alert-success">';
            echo '<strong>SQL Query untuk Insert ke Database:</strong><br>';
            echo '<div class="output-box">';
            echo 'INSERT INTO pegawai (Nama_Pegawai, Username, Password, ID_Jabatan, Status)<br>';
            echo 'VALUES (\'Nama User\', \'username\', \'' . htmlspecialchars($hash) . '\', 1, \'Aktif\');';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>

        <div class="mt-4 p-3 bg-light rounded">
            <h6><i class="bi bi-info-circle me-2"></i>Cara Penggunaan:</h6>
            <ol class="small mb-0">
                <li>Masukkan password yang diinginkan (contoh: admin123)</li>
                <li>Klik tombol "Generate Hash"</li>
                <li>Copy hash yang dihasilkan</li>
                <li>Paste hash tersebut ke field Password di database</li>
                <li>Atau gunakan SQL query yang sudah disediakan</li>
            </ol>
        </div>

        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Hash menggunakan algoritma bcrypt yang aman
            </small>
        </div>
    </div>

    <script>
        function copyHash() {
            const hashText = document.getElementById('hashOutput').textContent;
            navigator.clipboard.writeText(hashText).then(function() {
                alert('Hash berhasil di-copy ke clipboard!');
            }, function() {
                alert('Gagal copy hash. Silakan copy manual.');
            });
        }
    </script>
</body>
</html>