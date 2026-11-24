<?php
session_start();

// validasi.php hanya ada di folder client, jadi dipanggil dari sana
require_once __DIR__ . "/../client/validasi.php";

// Periksa login
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../auth/masuk.php");
    exit();
}

$id_user = $_SESSION["id_user"] ?? $_SESSION["Id_User"] ?? null;

if (!$id_user) {
    header("Location: ../auth/masuk.php");
    exit();
}

// Ambil informasi pengguna dari database
function ambilProfil($connect, $id_user)
{
    $query = mysqli_prepare(
        $connect,
        "SELECT Id_User, username, email, role
         FROM user WHERE Id_User = ?"
    );

    mysqli_stmt_bind_param($query, "i", $id_user);
    mysqli_stmt_execute($query);

    $result = mysqli_stmt_get_result($query);
    return mysqli_fetch_assoc($result);
}

$user = ambilProfil($connect, $id_user);

if (!$user) {
    session_destroy();
    header("Location: ../auth/masuk.php");
    exit();
}

// Hanya expert yang boleh membuka halaman ini
if (strtolower($user["role"]) !== "expert") {
    header("Location: ../client/profil.php");
    exit();
}

$error = "";
$modal_aktif = "";

// =====================================
// PROSES EDIT PROFIL
// =====================================

if (isset($_POST["ubah_profil"])) {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");

    $errors = [];

    // Validasi username dan email
    valUsername($errors, $username);
    valEmail($errors, $email);

    // Periksa username dan email milik akun lain
    if (empty($errors)) {

        $cek = mysqli_prepare(
            $connect,
            "SELECT Id_User FROM user
             WHERE (username = ? OR email = ?)
             AND Id_User != ?"
        );

        mysqli_stmt_bind_param(
            $cek,
            "ssi",
            $username,
            $email,
            $id_user
        );

        mysqli_stmt_execute($cek);
        $hasil_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil_cek) > 0) {
            $errors["akun"] = "Username atau email sudah digunakan akun lain.";
        }
    }

    // Jika tidak ada kesalahan, update database
    if (empty($errors)) {

        $update = mysqli_prepare(
            $connect,
            "UPDATE user
             SET username = ?, email = ?
             WHERE Id_User = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "ssi",
            $username,
            $email,
            $id_user
        );

        if (mysqli_stmt_execute($update)) {
            header("Location: profil.php?berhasil=profil");
            exit();
        } else {
            $error = "Gagal memperbarui profil.";
            $modal_aktif = "edit-profil";
        }

    } else {
        $error = implode("<br>", array_map(
            "htmlspecialchars",
            array_values($errors)
        ));

        $user["username"] = $username;
        $user["email"] = $email;
        $modal_aktif = "edit-profil";
    }
}


// =====================================
// PROSES UBAH PASSWORD
// =====================================

if (isset($_POST["ubah_password"])) {

    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    $errors = [];

    valPassword($errors, $password);
    valConfirmPassword($errors, $password, $confirm_password);

    if (empty($errors)) {

        $update = mysqli_prepare(
            $connect,
            "UPDATE user
             SET password = SHA2(?, 0)
             WHERE Id_User = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "si",
            $password,
            $id_user
        );

        if (mysqli_stmt_execute($update)) {
            header("Location: profil.php?berhasil=password");
            exit();
        } else {
            $error = "Gagal mengubah password.";
            $modal_aktif = "ubah-password";
        }

    } else {
        $error = implode("<br>", array_map(
            "htmlspecialchars",
            array_values($errors)
        ));

        $modal_aktif = "ubah-password";
    }
}


// Ambil ulang data terbaru dari database
// (kalau ada error edit profil, tetap tampilkan input terakhir pengguna)
if ($modal_aktif !== "edit-profil") {
    $user = ambilProfil($connect, $id_user);
}

// Notifikasi
$berhasil_profil = isset($_GET["berhasil"]) &&
                   $_GET["berhasil"] === "profil";

$berhasil_password = isset($_GET["berhasil"]) &&
                     $_GET["berhasil"] === "password";

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

    <!-- SIDEBAR -->
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

                    <a href="#edit-profil" class="btn-save">
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


<!-- =====================================
     MODAL EDIT PROFIL
===================================== -->

<div id="edit-profil"
     class="modal <?= $modal_aktif === 'edit-profil' ? 'tampil' : ''; ?>">

    <div class="password-modal">

        <h2>Edit Profil</h2>

        <p>Perbarui informasi akun RembugUTM kamu.</p>

        <?php if ($modal_aktif === "edit-profil" && !empty($error)): ?>
            <div class="error">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="profil.php#edit-profil">

            <div class="form-group">
                <label>Username</label>

                <input
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars($user["username"]); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($user["email"]); ?>"
                    required
                >
            </div>

            <div class="form-buttons">

                <button
                    type="submit"
                    name="ubah_profil"
                    class="btn-save">
                    Simpan Perubahan
                </button>

                <a href="profil.php" class="btn-back">
                    Batal
                </a>

            </div>

        </form>

    </div>

</div>


<!-- =====================================
     MODAL UBAH PASSWORD
===================================== -->

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


<!-- =====================================
     MODAL KONFIRMASI LOGOUT
===================================== -->

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