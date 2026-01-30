<?php
session_start();
require_once '../config/config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ../dashboard.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT ID_Pegawai, Nama_Pegawai, Username, Password, ID_Jabatan FROM pegawai WHERE Username = ? AND Status = 'Aktif'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['Password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['id_pegawai'] = $user['ID_Pegawai'];
                $_SESSION['nama_pegawai'] = $user['Nama_Pegawai'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['id_jabatan'] = $user['ID_Jabatan'];
                
                header('Location: ../dashboard.php'); 
                exit();
            } else {
                $error_message = 'Username atau password salah!';
            }
        } else {
            $error_message = 'Username atau password salah!';
        }
        $stmt->close();
    } else {
        $error_message = 'Harap isi semua field!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NZ Barbershop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Source Sans 3", sans-serif;
            background: #0a0e1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background Pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                repeating-linear-gradient(
                    90deg,
                    transparent,
                    transparent 2px,
                    rgba(212, 175, 55, 0.03) 2px,
                    rgba(212, 175, 55, 0.03) 4px
                ),
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(212, 175, 55, 0.03) 2px,
                    rgba(212, 175, 55, 0.03) 4px
                );
            z-index: 0;
        }
        
        /* Gradient Overlay */
        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse at center, rgba(45, 95, 93, 0.2) 0%, rgba(10, 14, 26, 0.9) 70%);
            z-index: 0;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
            position: relative;
            z-index: 1;
        }
        
        .login-card {
            background: linear-gradient(145deg, #1a2332 0%, #0f1419 100%);
            border-radius: 25px;
            box-shadow: 
                0 30px 90px rgba(0, 0, 0, 0.8),
                0 0 1px rgba(212, 175, 55, 0.5),
                inset 0 1px 1px rgba(212, 175, 55, 0.1);
            overflow: hidden;
            border: 2px solid rgba(212, 175, 55, 0.2);
            position: relative;
        }
        
        /* Decorative corner borders */
        .login-card::before,
        .login-card::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            border: 3px solid #d4af37;
            z-index: 1;
        }
        
        .login-card::before {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
            border-top-left-radius: 25px;
        }
        
        .login-card::after {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
            border-bottom-right-radius: 25px;
        }
        
        .login-header {
            background: linear-gradient(180deg, #2d5f5d 0%, #1a3937 100%);
            padding: 50px 30px 40px;
            text-align: center;
            position: relative;
            border-bottom: 3px solid #d4af37;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                #d4af37 50%, 
                transparent 100%);
        }
        
        .brand-logo-container {
            margin-bottom: 25px;
            position: relative;
        }
        
        .brand-logo {
            width: 140px;
            height: 140px;
            margin: 0 auto;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 10px 40px rgba(212, 175, 55, 0.4),
                0 0 0 8px rgba(212, 175, 55, 0.1),
                inset 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 4px solid #d4af37;
            position: relative;
            overflow: hidden;
        }
        
        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
        }
        
        /* Fallback icon jika gambar tidak ada */
        .brand-logo-icon {
            font-size: 60px;
            color: #2d5f5d;
        }
        
        .brand-title {
            font-family: 'Cinzel', serif;
            font-size: 36px;
            font-weight: 700;
            color: #d4af37;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            letter-spacing: 2px;
        }
        
        .brand-subtitle {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 16px;
            color: #c9a961;
            letter-spacing: 3px;
            opacity: 0.9;
            text-transform: uppercase;
        }
        
        .brand-est {
            font-size: 13px;
            color: #8b9098;
            margin-top: 5px;
            font-style: italic;
        }
        
        .login-body {
            padding: 45px 35px;
            background: linear-gradient(180deg, #1a2332 0%, #151b26 100%);
        }
        
        .welcome-text {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .welcome-text h4 {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            color: #d4af37;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .welcome-text p {
            font-size: 14px;
            color: #8b9098;
        }
        
        .form-floating {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-floating input {
            background: rgba(26, 35, 50, 0.8);
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            padding: 14px 20px;
            height: 60px;
            color: #d4af37;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-floating input::placeholder {
            color: #6b7280;
        }
        
        .form-floating input:focus {
            background: rgba(26, 35, 50, 1);
            border-color: #d4af37;
            box-shadow: 
                0 0 0 0.2rem rgba(212, 175, 55, 0.2),
                0 4px 12px rgba(212, 175, 55, 0.3);
            color: #d4af37;
        }
        
        .form-floating label {
            padding: 18px 20px;
            color: #8b9098;
            font-size: 14px;
        }
        
        .form-floating input:focus ~ label,
        .form-floating input:not(:placeholder-shown) ~ label {
            color: #d4af37;
        }
        
        .password-toggle {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8b9098;
            z-index: 10;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: #d4af37;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #d4af37 0%, #c9a961 50%, #d4af37 100%);
            background-size: 200% 100%;
            border: none;
            border-radius: 12px;
            color: #1a2332;
            font-weight: 700;
            padding: 16px;
            width: 100%;
            font-size: 17px;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 
                0 6px 20px rgba(212, 175, 55, 0.4),
                inset 0 -2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 
                0 10px 30px rgba(212, 175, 55, 0.6),
                inset 0 -2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 12px;
            border: 2px solid;
            padding: 14px 18px;
            margin-bottom: 25px;
            background: rgba(26, 35, 50, 0.9);
        }
        
        .alert-danger {
            background: rgba(220, 38, 38, 0.15);
            border-color: #dc2626;
            color: #fca5a5;
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.3), transparent);
        }
        
        .divider span {
            background: #1a2332;
            padding: 0 15px;
            color: #8b9098;
            font-size: 13px;
            position: relative;
            z-index: 1;
        }
        
        .login-footer {
            background: linear-gradient(180deg, #0f1419 0%, #0a0e14 100%);
            padding: 25px 30px;
            text-align: center;
            border-top: 2px solid rgba(212, 175, 55, 0.2);
        }
        
        .login-footer p {
            margin: 0;
            font-size: 13px;
            color: #6b7280;
        }
        
        .login-footer .brand-mark {
            color: #d4af37;
            font-weight: 600;
            font-family: 'Cinzel', serif;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 10px;
            }
            
            .brand-logo {
                width: 120px;
                height: 120px;
            }
            
            .brand-title {
                font-size: 28px;
            }
            
            .login-body {
                padding: 35px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="brand-logo-container">
                    <div class="brand-logo">
                        <!-- Ganti path sesuai lokasi logo Anda -->
                        <img src="../assets/img/logo-nz.png" alt="NZ Barbershop" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="bi bi-scissors brand-logo-icon" style="display: none;"></i>
                    </div>
                </div>
                <h1 class="brand-title">NZ BARBERSHOP</h1>
                <p class="brand-subtitle">Premium Grooming</p>
                <p class="brand-est">Established 2025</p>
            </div>
            
            <div class="login-body">
                <div class="welcome-text">
                    <h4>Welcome Back</h4>
                    <p>Sign in to continue to your account</p>
                </div>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?php echo $error_message; ?></div>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                        <label for="username"><i class="bi bi-person-circle me-2"></i>Username</label>
                    </div>
                    
                    <div class="form-floating position-relative">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="bi bi-shield-lock me-2"></i>Password</label>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                        </span>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </form>
                
                <div class="divider">
                    <span>Secure Login</span>
                </div>
            </div>
            
            <div class="login-footer">
                <p>
                    <i class="bi bi-shield-check me-1"></i>
                    &copy; <?php echo date('Y'); ?> <span class="brand-mark">NZ Barbershop</span>. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        }
        
        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>