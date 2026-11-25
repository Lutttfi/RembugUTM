<?php
require_once __DIR__ . "/validasi.php";

// Cek login + pastikan role expert (data pengguna dikembalikan)
$user = wajibLogin($connect, "client");

$error = "";
$modal_aktif = "";

// Proses ubah password
if (isset($_POST["ubah_password"])) {
    if (prosesUbahPassword($connect, $user["Id_User"], $error)) {
        header("Location: profil.php?berhasil=password");
        exit();
    }
    $modal_aktif = "ubah-password";
}

// Notifikasi
$berhasil_profil = ($_GET["berhasil"] ?? "") === "profil";
$berhasil_password = ($_GET["berhasil"] ?? "") === "password";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - RembugUTM</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div class="layout">
    <aside class="sidebar">
        <a href="../index.php" class="logo">
            <span class="logo-icon">R</span>
            Rembug<b>UTM</b>
        </a>

        <nav class="menu">
            <a href="beranda.php">
                <span>Beranda</span>
            </a>

            <a href="diskusi.php">
                <span>Laporan</span>
            </a>

            <a href="riwayat.php">
                <span>Riwayat Laporan</span>
            </a>

            <a href="profil.php" class="active">
                <span>Profil Saya</span>
            </a>
        </nav>

        <a href="../auth/logout.php" class="logout">
            <span>Keluar</span>
        </a>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-title">
                <a href="../index.php" class="logo logo-dark">
                    <span class="logo-icon">R</span>
                    Rembug<b>UTM</b>
                </a>
            </div>

            <div class="profile">
                <a href="profil.php" class="avatar">
                    <?= strtoupper(substr(htmlspecialchars($user["username"]), 0, 1)); ?>
                </a>
            </div>
        </header>

        <section class="content">
            <div class="profile-header">
                <h1>Profil Saya</h1>
                <p>Lihat dan kelola informasi akun RembugUTM kamu.</p>
            </div>

            <div class="profile-card">
                <div class="profile-avatar">
                    <?= strtoupper(substr(htmlspecialchars($user["username"]), 0, 1)); ?>
                </div>

                <h3>
                    <?= htmlspecialchars($user["username"]); ?>
                </h3>

                <span class="role">
                    <?= htmlspecialchars(ucfirst($user["role"])); ?>
                </span>

                <!-- NOTIFIKASI -->
                <?php if ($berhasil_profil): ?>
                    <div class="success">
                        Profil berhasil diperbarui.
                    </div>
                <?php endif; ?>

                <?php if ($berhasil_password): ?>
                    <div class="success">
                        Password berhasil diubah.
                    </div>
                <?php endif; ?>

                <!-- INFORMASI PROFIL -->
                <div class="profile-info">
                    <div class="info-group">
                        <label>Username</label>
                        <p>
                            <?= htmlspecialchars($user["username"]); ?>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Email</label>
                        <p>
                            <?= htmlspecialchars($user["email"]); ?>
                        </p>
                    </div>

                    <div class="info-group">
                        <label>Role</label>
                        <p>
                            <?= htmlspecialchars(ucfirst($user["role"])); ?>
                        </p>
                    </div>
                </div>

                <!-- TOMBOL PROFIL -->
                <div class="profile-buttons">
                    <a href="edit_profil.php" class="btn-save">
                        Edit Profil
                    </a>

                    <a href="#ubah-password" class="btn-password">
                        Ubah Password
                    </a>
                </div>
            </div>
        </section>
    </main>
</div>

<div id="ubah-password"
     class="modal <?= $modal_aktif === 'ubah-password' ? 'tampil' : ''; ?>">
    <div class="password-modal">
        <h2>Ubah Password</h2>
        <p>Masukkan password baru untuk akun kamu.</p>
        <?php if ($modal_aktif === "ubah-password" && !empty($error)): ?>
            <div class="error">
                <?= $error; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="profil.php#ubah-password">
            <div class="form-group">
                <label>Password Baru</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password baru"
                    required
                >
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Ulangi password baru"
                    required
                >
            </div>

            <div class="form-buttons">
                <button
                    type="submit"
                    name="ubah_password"
                    class="btn-save">
                    Simpan Password
                </button>

                <a href="profil.php" class="btn-back">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<div id="konfirmasi-logout" class="modal">
    <div class="modal-content">
        <h2>Konfirmasi Logout</h2>
        <p>
            Apakah kamu yakin ingin keluar dari akun RembugUTM?
        </p>
        <div class="modal-buttons">
            <a href="#" class="btn-batal">
                Batal
            </a>

            <a href="../auth/logout.php" class="btn-keluar">
                Ya, Keluar
            </a>
        </div>
    </div>
</div>
</body>
</html>