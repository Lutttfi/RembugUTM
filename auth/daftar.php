<?php
require "../database/koneksi.php";
require "../client/validasi.php";
session_start();
$errors = [];
$role = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username =  htmlspecialchars($_POST["username"]?? "");
    $email = htmlspecialchars($_POST["email"] ?? "") ;
    $password = htmlspecialchars($_POST["password"] ?? "");
    $confirm_password = htmlspecialchars($_POST["confirm_password"] ?? "");
    $role = htmlspecialchars($_POST["role"] ?? "");
    $nomor = htmlspecialchars($_POST["nomor"] ?? "");

    // Validasi input
    $errors = valUsername($errors, $username);
    $errors = valEmail($errors, $email);
    $errors = valPassword($errors, $password);
    $errors = valConfirmPassword($errors, $password, $confirm_password);
    $errors = valRole($errors, $role);
    $errors = valNomor($errors, $nomor);

  // Cek username jika validasi username berhasil
    if (!isset($errors["username"])) {

        $sql = "SELECT username FROM user WHERE username = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors["username"] = "Username sudah digunakan!";
        }

        $stmt->close();
    }

// Cek email jika validasi email berhasil
    if (!isset($errors["email"])) {

        $sql = "SELECT email FROM user WHERE email = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors["email"] = "Email sudah terdaftar!";
        }

        $stmt->close();
    }

// Jika tidak ada error, simpan data
    if (empty($errors)) {

        $sql = "INSERT INTO user (username, email, password, role)
                VALUES (?, ?, SHA2(?, 0), ?)";

        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password, $role);

        if ($stmt->execute()) {

            $_SESSION["pesan_daftar"] = "Pendaftaran berhasil! Silakan login.";

            header("Location: masuk.php");
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <link rel="stylesheet" href="../css/daftar.css">
</head>
<body>
    <div class="register-container">
        <!-- BAGIAN KIRI -->
        <div class="register-left">
            <div class="left-content">
                <p class="subtitle">MULAI PERJALANAN ANDA</p>
                <h1>
                    Suarakan aspirasi,
                    wujudkan lingkungan
                    yang lebih baik.
                </h1>
                <p class="description">
                    Bergabung bersama masyarakat lainnya
                    untuk menyampaikan aspirasi dan
                    membangun lingkungan masyarakat yang lebih baik.
                </p>
                <ul>
                    <li>Sampaikan aspirasi dengan mudah</li>
                    <li>Pantau perkembangan laporan</li>
                    <li>Terhubung dengan masyarakat lainnya</li>
                </ul>
            </div>
        </div>
        <!-- BAGIAN KANAN -->
        <div class="register-right">
            <div class="form-content">
                <h1>Buat Akun</h1>
                <p class="form-subtitle">
                    Sudah punya akun?
                    <a href="masuk.php">Masuk di sini</a>
                </p>
                <form action="daftar.php" method="post">
                    <!-- FORM MASUKAN USERNAME -->
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" value="<?= isset($errors["username"]) ? "" : trim($_POST["username"] ?? "") ?>">
                    <span class="error"><?= $errors["username"] ?? "" ?></span>
                    <!-- FORM MASUKAN EMAIL -->
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" placeholder="Masukkan email" value="<?= isset($errors["email"]) ? "" : trim($_POST["email"] ?? "") ?>">
                    <span class="error"><?= $errors["email"] ?? "" ?></span><br><br>   
                    <!-- FORM MASUKAN PASSWORD -->
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" value="<?= isset($errors["password"]) ? "" : trim($_POST["password"] ?? "") ?>">
                    <span class="error"><?= $errors["password"] ?? "" ?></span><br><br> 
                    <!-- FORM MASUKAN CONFIRM PASSWORD -->
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password" value="<?= isset($errors["confirm_password"]) ? "" : trim($_POST["confirm_password"] ?? "") ?>">
                    <span class="error"><?= $errors["confirm_password"] ?? "" ?></span><br><br> 
                    <!-- FORM MASUKAN NOMOR TELEPON -->
                    <label for="nomor">Nomor Telepon:</label>
                    <input type="text" id="nomor" name="nomor" placeholder="Masukkan nomor telepon" value="<?= isset($errors["nomor"]) ? "" : trim($_POST["nomor"] ?? "") ?>">
                    <span class="error"><?= $errors["nomor"] ?? "" ?></span><br><br> 
                    <!-- FORM ROLE -->
                    <label for="role">Daftar Sebagai:</label>
                    <select id="role" name="role">
                        <option value="">-- Pilih Role --</option>
                        <option value="client" <?= (isset($role) && $role === "client") ? "selected" : "" ?>>Client</option>
                        <option value="expert" <?= (isset($role) && $role === "expert") ? "selected" : "" ?>>Expert</option>
                    </select>
                    <span class="error"><?= $errors["role"] ?? "" ?></span><br><br>
                    <input type="submit" value="Daftar">
                    <a href="../index.php" class="btn-kembali">Kembali ke Halaman Utama</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>