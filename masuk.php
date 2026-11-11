<?php
require "koneksi.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Jika email dan password kosong
    if (empty($email) && empty($password)) {
        $error = "Email dan password wajib diisi.";
    }

    // Jika salah satu kosong
    else if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi.";
    }

    // Jika keduanya sudah diisi
    else {

        $sql = "SELECT * FROM user
                WHERE Email = ? AND Password = SHA2(?, 0)";

        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();

        $result = $stmt->get_result();

        // Jika email dan password cocok
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

<form action="masuk.php" method="post">

    <label for="email">Email:</label>
    <input type="text" id="email" name="email" value="<?= isset($errors["email"]) ? "" : trim($_POST["email"] ?? "") ?>">
    <br><br>

    <label for="password">Password:</label>
    <input type="text" id="password" name="password" value="<?= isset($errors["password"]) ? "" : trim($_POST["password"] ?? "") ?>">
    <br><br>

    <span style="color: red;">
        <?= $error ?>
    </span>
    <br><br>

    <input type="submit" value="Masuk">

</form>