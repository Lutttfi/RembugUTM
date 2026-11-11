<?php
require "koneksi.php";
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) && empty($password)) {
        $error = "Email dan password wajib diisi.";
    }
    else if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi.";
    }
    else {

        $sql = "SELECT * FROM user
                WHERE Email = ? AND Password = SHA2(?, 0)";

        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $data = $result->fetch_assoc();

            $_SESSION["Id_User"] = $data["Id_User"];
            $_SESSION["Username"] = $data["Username"];
            $_SESSION["Email"] = $data["Email"];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Email dan password salah";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <a href="#beranda" class="logo"><span class="logo-icon">R</span> Rembug<b>UTM</b></a>
    </header>

    <form action="masuk.php" method="post">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?= isset($errors["email"]) ? "" : trim($_POST["email"] ?? "") ?>">
        <br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" value="<?= isset($errors["password"]) ? "" : trim($_POST["password"] ?? "") ?>">
        <br><br>
        <span style="color: red;">
            <?= $error ?>
        </span>
        <br><br>
        <input type="submit" value="Masuk">
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
