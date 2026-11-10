<?php
session_start();
$errors  = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$old     = isset($_SESSION['old']) ? $_SESSION['old'] : [];
unset($_SESSION['errors'], $_SESSION['success'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirasi Kampus - Login</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- File CSS Eksternal -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="main-container">
        
        <!-- Sisi Kiri (Branding) -->
        <div class="brand-panel">
            <div class="brand-header">
                <div class="logo-box">
                    <i class="fa-solid fa-message"></i>
                </div>
                <h1 class="brand-title">Aspirasi Kampus</h1>
            </div>

            <div class="brand-slogan">
                <h2>Suaramu,<br>Perubahan Kita.</h2>
                <p>Sampaikan aspirasi, kritik, dan saran untuk kampus yang lebih baik.</p>
            </div>

            <div class="illustration-box">
                <svg width="220" height="90" viewBox="0 0 220 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 80H210V85H10V80Z" fill="#3B82F6"/>
                    <rect x="75" y="25" width="70" height="55" rx="2" fill="#2563EB"/>
                    <path d="M110 5L150 25H70L110 5Z" fill="#60A5FA"/>
                    <rect x="85" y="35" width="12" height="12" fill="#DBEAFE"/>
                    <rect x="123" y="35" width="12" height="12" fill="#DBEAFE"/>
                    <rect x="85" y="55" width="12" height="12" fill="#DBEAFE"/>
                    <rect x="123" y="55" width="12" height="12" fill="#DBEAFE"/>
                    <rect x="103" y="58" width="14" height="22" rx="1" fill="#1D4ED8"/>
                    <rect x="35" y="40" width="40" height="40" rx="2" fill="#1D4ED8"/>
                    <rect x="39" y="48" width="10" height="10" fill="#93C5FD"/>
                    <rect x="57" y="48" width="10" height="10" fill="#93C5FD"/>
                    <rect x="145" y="40" width="40" height="40" rx="2" fill="#1D4ED8"/>
                    <rect x="153" y="48" width="10" height="10" fill="#93C5FD"/>
                    <rect x="171" y="48" width="10" height="10" fill="#93C5FD"/>
                </svg>
            </div>
        </div>

        <!-- Sisi Kanan (Form Login) -->
        <div class="form-panel">
            
            <div class="tab-nav">
                <a href="login.php" class="tab-link active">Login</a>
                <a href="daftar.php" class="tab-link inactive">Daftar</a>
            </div>

            <h3 class="form-title">Masuk ke Akun Anda</h3>

            <?php if (!empty($success)): ?>
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors['login'])): ?>
                <div class="alert-box alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($errors['login']) ?></span>
                </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST" class="auth-form">
                
                <!-- Pilihan Role -->
                <div class="role-grid">
                    <label class="role-card">
                        <input type="radio" name="role" value="client" checked>
                        <div class="role-box">
                            <div class="role-icon"><i class="fa-solid fa-user"></i></div>
                            <div>
                                <p class="role-title">Sebagai Client</p>
                                <p class="role-subtitle">(Mahasiswa)</p>
                            </div>
                        </div>
                    </label>

                    <label class="role-card">
                        <input type="radio" name="role" value="expert">
                        <div class="role-box">
                            <div class="role-icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <div>
                                <p class="role-title">Sebagai Expert</p>
                                <p class="role-subtitle">(Admin)</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Input Email/NIM -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fa-regular fa-envelope"></i></span>
                        <input type="text" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                               class="form-input" placeholder="Email atau NIM" required>
                    </div>
                </div>

                <!-- Input Password -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" id="login_password" 
                               class="form-input" placeholder="Password" required>
                        <button type="button" onclick="togglePassword('login_password', 'eyeIconLogin')" class="password-toggle">
                            <i id="eyeIconLogin" class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>

                <button type="submit" class="btn-primary">Masuk</button>
            </form>

            <div class="divider">
                <div class="divider-line"><div class="divider-line-inner"></div></div>
                <div class="divider-text-wrapper"><span class="divider-text">atau</span></div>
            </div>

            <button type="button" class="btn-google">
                <svg class="google-icon" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                Masuk dengan Google
            </button>

            <p class="footer-text">
                Belum punya akun? <a href="register.php" class="footer-link">Daftar di sini</a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }
    </script>
</body>
</html>