<?php
require "koneksi.php";
require "validasi.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errors = [];

    $username =  $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $errors = valUsername($errors, $username);
    $errors = valEmail($errors, $email);
    $errors = valPassword($errors, $password);
    $errors = valConfirmPassword($errors, $password, $confirm_password);

    if (empty($errors)) {
        $sql = "INSERT INTO user (username, email, password)
        VALUES (?, ?, SHA2(?, 0))";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="navbar">
        <a href="#beranda" class="logo"><span class="logo-icon">R</span> Rembug<b>UTM</b></a>
    </header>

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
        <input type="submit" value="Daftar">
    </form>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-wrap">
        <div class="footer-brand">
            <a href="#beranda" class="logo"><span class="logo-icon">R</span> Rembug<b>UTM</b></a>
            <p>Platform aspirasi dan laporan mahasiswa untuk kampus yang lebih baik.</p>
        </div>

        <div class="footer-col">
            <h4>Navigasi</h4>
            <a href="#beranda">Beranda</a>
            <a href="#trending">Trending</a>
            <a href="#tentang">Tentang</a>
            <a href="#kontak">Kontak</a>
        </div>

        <div class="footer-col">
            <h4>Layanan</h4>
            <a href="#">Buat Laporan</a>
            <a href="#">Pantau Laporan</a>
            <a href="#">Pertanyaan Umum</a>
        </div>
        </div>

        <p class="copyright">© 2026 RembugUTM. Semua hak dilindungi.</p>
    </footer>
</body>
</html>