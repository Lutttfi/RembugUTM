
<?php
include "koneksi.php";

$pesan = "";

if (isset($_POST['daftar'])) {
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Validasi input
    if ($username == "" || $nama == "" || $email == "" || $password == "") {
        $pesan = "Semua kolom wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = "Format email tidak valid!";
    } elseif (strlen($password) < 8) {
        $pesan = "Password minimal 8 karakter!";
    } elseif ($password !== $konfirmasi) {
        $pesan = "Konfirmasi password tidak sesuai!";
    } else {

        // Cek username dan email
        $cek = mysqli_prepare($conn,
            "SELECT Id_User FROM User
             WHERE Username = ? OR Email = ?"
        );

        mysqli_stmt_bind_param($cek, "ss", $username, $email);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) > 0) {
            $pesan = "Username atau email sudah terdaftar!";
        } else {

            // Enkripsi password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Role otomatis Client
            $role = "Client";

            // Simpan data pengguna
            $query = mysqli_prepare($conn,
                "INSERT INTO User
                (Username, Password, Nama_Lengkap, Email, Role)
                VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $query,
                "sssss",
                $username,
                $password_hash,
                $nama,
                $email,
                $role
            );

            if (mysqli_stmt_execute($query)) {
                echo "<script>
                    alert('Pendaftaran berhasil! Silakan masuk.');
                    window.location='masuk.php';
                </script>";
                exit;
            } else {
                $pesan = "Pendaftaran gagal. Silakan coba lagi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Aspirasi Kampus</title>
    <link rel="stylesheet" href="daftar.css">
</head>
<body>

<div class="register-container">

    <div class="register-logo">A</div>

    <h2>Buat Akun Baru</h2>
    <p class="subtitle">
        Bergabunglah untuk menyampaikan aspirasi kampus.
    </p>

    <?php if ($pesan != "") { ?>
        <div class="pesan">
            <?= htmlspecialchars($pesan); ?>
        </div>
    <?php } ?>

    <form method="POST" action="">

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap"
                   placeholder="Masukkan nama lengkap"
                   value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username"
                   placeholder="Buat username"
                   value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email"
                   placeholder="Masukkan email"
                   value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password"
                   placeholder="Minimal 8 karakter"
                   required>
        </div>

        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="konfirmasi_password"
                   placeholder="Masukkan ulang password"
                   required>
        </div>

        <button type="submit" name="daftar">
            Daftar Sekarang
        </button>

    </form>

    <p class="login-link">
        Sudah memiliki akun?
        <a href="masuk.php">Masuk di sini</a>
    </p>

</div>

</body>
</html>
