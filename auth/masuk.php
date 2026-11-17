<?php
require "../client/validasi.php";
session_start();
$error = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username =  htmlspecialchars($_POST["username"]?? "");
    $password = htmlspecialchars($_POST["password"]?? "");

    $errors = valUsername($errors, $username);
    $errors = valPassword($errors, $password);

    if (empty($errors)) {
        $sql = "SELECT username, password, role, Id_User FROM user WHERE username = ? AND password = SHA2(?, 0)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
            
    if ($data) {

        $_SESSION["login"] = true;
        $_SESSION["id_user"] = $data["id_user"];
        $_SESSION["role"] = $data["role"];

        if ($data["role"] === "client") {
            header("Location: ../client/beranda.php");
            exit();

        } else if ($data["role"] === "expert") {
            header("Location: ../expert/beranda.php");
            exit();
        }

    } else {
        echo "Username atau password salah!";
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
                <h1>Masuk ke Akun</h1>
                <form action="masuk.php" method="post">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?= isset($errors["username"]) ? "" : trim($_POST["username"] ?? "") ?>">
                    <span style="color: red;"><?= $errors["username"] ?? "" ?></span><br><br>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="<?= isset($errors["password"]) ? "" : trim($_POST["password"] ?? "") ?>">
                    <span style="color: red;"><?= $errors["password"] ?? "" ?></span><br><br> 
                    <input type="submit" value="Masuk Ke Dashbord">
                </form>
            </div>
        </div>
    </div>
</body>
</html>