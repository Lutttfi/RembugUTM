<?php
session_start();
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old = isset($_SESSION['old']) ? $_SESSION['old'] : [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirasi Kampus - Daftar</title>
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
                <p>Bergabunglah untuk menyampaikan aspirasi, kritik, dan saran demi kampus yang lebih baik.</p>
            </div>

            <!-- Ilustrasi Gedung -->
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

        <!-- Sisi Kanan (Form Registrasi) -->
        <div class="form-panel">
            
            <!-- Tab Navigation -->
            <div class="tab-nav">
                <a href="login.php" class="tab-link inactive">Login</a>
                <a href="register.php" class="tab-link active">Daftar</a>
            </div>

            <h3 class="form-title">Buat Akun Baru</h3>

            <form action="proses_register.php" method="POST" class="auth-form">
                
                <!-- Username -->
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fa-regular fa-user"></i></span>
                        <input type="text" name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" 
                               class="form-input <?= isset($errors['username']) ? 'input-error' : '' ?>" 
                               placeholder="Masukkan Username Anda" required>
                    </div>
                    <?php if (isset($errors['username'])): ?>
                        <p class="error-text"><?= $errors['username'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label">Email atau NIM</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fa-regular fa-envelope"></i></span>
                        <input type="text" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                               class="form-input <?= isset($errors['email']) ? 'input-error' : '' ?>" 
                               placeholder="Email atau NIM Anda" required>
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <p class="error-text"><?= $errors['email'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password & Konfirmasi Password -->
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" id="password"
                                   class="form-input <?= isset($errors['password']) ? 'input-error' : '' ?>" 
                                   placeholder="Min 6 karakter" required>
                            <button type="button" onclick="togglePassword('password', 'eyeIcon1')" class="password-toggle">
                                <i id="eyeIcon1" class="fa-regular fa-eye-slash"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <p class="error-text"><?= $errors['password'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fa-solid fa-shield-halved"></i></span>
                            <input type="password" name="confirm_password" id="confirm_password"
                                   class="form-input <?= isset($errors['confirm_password']) ? 'input-error' : '' ?>" 
                                   placeholder="Ulangi Password" required>
                            <button type="button" onclick="togglePassword('confirm_password', 'eyeIcon2')" class="password-toggle">
                                <i id="eyeIcon2" class="fa-regular fa-eye-slash"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <p class="error-text"><?= $errors['confirm_password'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Daftar Sekarang</button>
            </form>

            <p class="footer-text">
                Sudah punya akun? <a href="login.php" class="footer-link">Masuk di sini</a>
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