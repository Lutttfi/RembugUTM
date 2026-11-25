<?php
require_once __DIR__ . "/../client/validasi.php";

// Cek login + pastikan role expert (data pengguna dikembalikan)
$user = wajibLogin($connect, "expert");

$error = "";
$form = [
    "username" => $user["username"],
    "email" => $user["email"],
];

// Proses simpan: kalau berhasil langsung kembali ke profil.php
if (isset($_POST["ubah_profil"])) {
    if (prosesEditProfil($connect, $user["Id_User"], $form, $error)) {
        header("Location: profil.php?berhasil=profil");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - RembugUTM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <a href="../index.php" class="logo">
            <span class="logo-icon">R</span>
            Rembug<b>Rakyat</b>
        </a>
        <nav class="menu">
            <a href="beranda.php">
                <span>Beranda</span>
            </a>
            <a href="diskusi.php">
                <span>Laporan</span>
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
                    Rembug<b>Rakyat</b>
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
        </section>
    </main>
</div>

<div class="modal tampil">
    <div class="password-modal">
        <div class="modal-head">
            <h2>Edit Profil</h2>
            <a href="profil.php" class="btn-close" aria-label="Kembali ke profil">
                &times;
            </a>
        </div>
        <p>Perbarui informasi akun RembugUTM kamu.</p>
        <?php if (!empty($error)): ?>
            <div class="error">
                <?= $error; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="edit_profil.php">
            <div class="form-group">
                <label>Username</label>
                <input
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars($form["username"]); ?>"
                    required
                >
            </div>
            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($form["email"]); ?>"
                    required
                >
            </div>
            <div class="form-buttons">
                <button
                    type="submit"
                    name="ubah_profil"
                    class="btn-save">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>