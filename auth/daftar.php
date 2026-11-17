<?php
require "../client/validasi.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errors = [];

    $username =  $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role = $_POST["role"];

    $errors = valUsername($errors, $username);
    $errors = valEmail($errors, $email);
    $errors = valPassword($errors, $password);
    $errors = valConfirmPassword($errors, $password, $confirm_password);
    $errors = valRole($errors, $role);
    
    // Cek username
    $sql = "SELECT username FROM USER WHERE username = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors["username"] = "Username sudah digunakan!";
    }

    // Cek email
    $sql = "SELECT email FROM USER WHERE email = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors["email"] = "Email sudah terdaftar!";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO user (username, email, password, role)
        VALUES (?, ?, SHA2(?, 0), ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <link rel="stylesheet" href="../client/css/daftar.css">
</head>
<body>
    <div class="register-container">
        <!-- BAGIAN KIRI -->
        <div class="register-left">
            <div class="left-content">
                <p class="subtitle">MULAI PERJALANAN ANDA</p>
                <h1>
                    Suarakan aspirasi,
                    wujudkan kampus
                    yang lebih baik.
                </h1>
                <p class="description">
                    Bergabung bersama mahasiswa lainnya
                    untuk menyampaikan aspirasi dan
                    membangun lingkungan kampus yang lebih baik.
                </p>
                <ul>
                    <li>Sampaikan aspirasi dengan mudah</li>
                    <li>Pantau perkembangan laporan</li>
                    <li>Terhubung dengan mahasiswa lainnya</li>
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
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?= isset($errors["username"]) ? "" : trim($_POST["username"] ?? "") ?>">
                    <span style="color: red;"><?= $errors["username"] ?? "" ?></span><br><br>
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" value="<?= isset($errors["email"]) ? "" : trim($_POST["email"] ?? "") ?>">
                    <span style="color: red;"><?= $errors["email"] ?? "" ?></span><br><br>    
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="<?= isset($errors["password"]) ? "" : trim($_POST["password"] ?? "") ?>">
                    <span style="color: red;"><?= $errors["password"] ?? "" ?></span><br><br> 
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" value="<?= isset($errors["confirm_password"]) ? "" : trim($_POST["confirm_password"] ?? "") ?>">
                    <span style="color: red;"><?= $errors["confirm_password"] ?? "" ?></span><br><br> 
                    <label for="role">Daftar Sebagai:</label>
                    <select id="role" name="role">
                        <option value="">-- Pilih Role --</option>
                        <option value="client" <?= (isset($role) && $role === "client") ? "selected" : "" ?>>Client</option>
                        <option value="expert" <?= (isset($role) && $role === "expert") ? "selected" : "" ?>>Expert</option>
                    </select>
                    <span style="color: red;"><?= $errors["role"] ?? "" ?></span><br><br>
                    <input type="submit" value="Daftar">
                </form>
            </div>
        </div>
    </div>
</body>
</html>