<?php
require "../database/koneksi.php";


// functions untuk mengupdate data berdasarkan ID
function update_buku($Id_User, $Username, $Password, $Email, $Role)
{
    global $connect;
    $query = "UPDATE user SET
			username='$Username',
			email='$Email',
			password=SHA2('$Password', 0),
			role='$Role'
			WHERE id_user=$Id_User";

    mysqli_query($connect, $query);
}

function requiredCheck($field)
{
    return trim($field) === "";
}

function valUsername(&$errors, $field)
{
    $pattern = "/^[a-zA-Z]+$/";
    if (requiredCheck($field)) {
        $errors["username"] = "Username tidak boleh kosong";
    } else if (!preg_match($pattern, $field)) {
        $errors["username"] = "Username hanya string saja";
    }
    return $errors;
}

function valEmail(&$errors, $field)
{
    $pattern = "/^[a-z0-9]+@[a-z]+\.[a-z]{3}$/i"; 
    if (requiredCheck($field)) {
        $errors["email"] = "Email tidak boleh kosong";
    } else if (!preg_match($pattern, $field)) {
        $errors["email"] = "Email tidak valid";
    }
    return $errors;
}

function valPassword(&$errors, $field)
{
    $pattern = "/^[a-zA-Z0-9]+$/";

    if (requiredCheck($field)) {
        $errors["password"] = "Password tidak boleh kosong";
    } else if (!preg_match($pattern, $field)) {
        $errors["password"] = "Password hanya boleh berisi huruf dan angka";
    } else if (strlen($field) < 8) {
        $errors["password"] = "Password minimal 8 karakter";
    }
    return $errors;
}

function valConfirmPassword(&$errors, $password, $confirm_password)
{
    if (requiredCheck($confirm_password)) {
        $errors["confirm_password"] = "Konfirmasi password tidak boleh kosong";
    } else if ($password !== $confirm_password) {
        $errors["confirm_password"] = "Konfirmasi password tidak sesuai";
    }
    return $errors;
}

function valRole(&$errors, $field)
{
    $allowed_roles = ["client", "expert"];
    if (requiredCheck($field)) {
        $errors["role"] = "Role tidak boleh kosong";
    } else if (!in_array($field, $allowed_roles)) {
        $errors["role"] = "Role tidak valid";
    }
    return $errors;
}
?>