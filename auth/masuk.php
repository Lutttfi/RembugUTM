<?php
require "../client/validasi.php";

session_start();
$errors = [];
$pesan_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username =  htmlspecialchars($_POST["username"]?? "");
    $password = htmlspecialchars($_POST["password"]?? "");

    // VALIDASI INPUT
    $errors = valUsername($errors, $username);
    $errors = valPassword($errors, $password);

    // JIKA TIDAK ADA ERROR, MAKA DATA AKAN DICARI DI DATABASE
    if (empty($errors)) {
        $sql = "SELECT username, password, role,
        Id_User AS id_user
        FROM user
        WHERE username = ? AND password = SHA2(?, 0)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
      
    // KONEKSI
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
        $pesan_error = "Username atau password salah!";
    }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <a href="#beranda" class="logo"><span class="logo-icon">R</span> Rembug<b>Rakyat</b></a>
    </header>
    <!-- CONTENT -->
    <content class="register">
        <div class="form-content">
            <h1>Masuk ke Akun</h1>
            <p class="form-subtitle"> Belum punya akun? <a href="daftar.php">Daftar di sini</a></p>
            <p class="error-login"><?= $pesan_error ?></p>
            <form action="masuk.php" method="post">
                <!-- FORM MASUKAN USERNAME -->
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" value="<?= isset($errors["username"]) ? "" : trim($_POST["username"] ?? "") ?>">
                <span class="error"><?= $errors["username"] ?? "" ?></span><br>
                <!-- FORM MASUKAN PASSWORD -->
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" value="<?= isset($errors["password"]) ? "" : trim($_POST["password"] ?? "") ?>">
                <span class="error"><?= $errors["password"] ?? "" ?></span><br>
                <input type="submit" value="Masuk Ke Beranda">
                <a href="../index.php" class="btn-kembali">Kembali ke Halaman Utama</a>
            </form>
        </div>
    </content>
</body>
</html>