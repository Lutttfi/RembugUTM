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

// Validasi server-side untuk form diskusi
function valDiskusi($errors, $jenis, $judul, $kategori, $isi, $cek_jenis = true)
{
    if ($cek_jenis && (requiredCheck($jenis) || !in_array($jenis, ['Diskusi', 'Pertanyaan']))) {
        $errors["jenis"] = "Jenis topik wajib dipilih.";
    }

    if (requiredCheck($judul)) {
        $errors["judul"] = "Judul diskusi wajib diisi.";
    } elseif (strlen(trim($judul)) < 5) {
        $errors["judul"] = "Judul diskusi minimal 5 karakter.";
    }

    if (requiredCheck($kategori)) {
        $errors["kategori"] = "Kategori wajib dipilih.";
    } elseif (!is_numeric($kategori)) {
        $errors["kategori"] = "Format kategori tidak valid.";
    }

    if (requiredCheck($isi)) {
        $errors["isi"] = "Isi diskusi wajib diisi.";
    } elseif (strlen(trim($isi)) < 15) {
        $errors["isi"] = "Isi diskusi terlalu singkat, minimal 15 karakter.";
    }

    return $errors;
}

// 1. PROSES BUAT TOPIK BARU
if (isset($_POST['buat_topik'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include '../database/koneksi.php';

    if (!isset($_SESSION['Id_User']) && !isset($_SESSION['id_user'])) {
        header("Location: ../auth/masuk.php");
        exit;
    }

    $id_user     = $_SESSION['Id_User'] ?? $_SESSION['id_user'];
    $jenis       = trim($_POST['jenis_laporan'] ?? '');
    $judul_input = trim($_POST['judul'] ?? '');
    $id_kategori = trim($_POST['kategori'] ?? '');
    $isi         = trim($_POST['isi'] ?? '');

    $errors = [];
    $errors = valDiskusi($errors, $jenis, $judul_input, $id_kategori, $isi, true);

    // Validasi & Upload Lampiran
    $nama_lampiran = NULL;
    if (empty($errors) && isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['lampiran']['tmp_name'];
            $file_name = $_FILES['lampiran']['name'];
            $file_size = $_FILES['lampiran']['size'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
            $max_size    = 5 * 1024 * 1024;

            if (!in_array($file_ext, $allowed_ext)) {
                $errors["lampiran"] = "Format lampiran tidak didukung! Gunakan JPG, PNG, atau PDF.";
            } elseif ($file_size > $max_size) {
                $errors["lampiran"] = "Ukuran file lampiran maksimal 5MB.";
            } else {
                $upload_dir = '../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $nama_lampiran = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
                $target_path   = $upload_dir . $nama_lampiran;

                if (!move_uploaded_file($file_tmp, $target_path)) {
                    $errors["lampiran"] = "Gagal mengunggah file lampiran ke server.";
                }
            }
        } else {
            $errors["lampiran"] = "Terjadi kesalahan saat mengunggah lampiran.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors']   = $errors;
        $_SESSION['old_data'] = [
            'jenis_laporan' => $jenis,
            'judul'         => $judul_input,
            'kategori'      => $id_kategori,
            'isi'           => $isi
        ];
        header("Location: diskusi.php");
        exit;
    }

    $jenis_safe       = mysqli_real_escape_string($connect, $jenis);
    $judul_safe       = mysqli_real_escape_string($connect, $judul_input);
    $id_kategori_safe = mysqli_real_escape_string($connect, $id_kategori);
    $isi_safe         = mysqli_real_escape_string($connect, $isi);

    $judul_topik  = "[$jenis_safe] " . $judul_safe;
    $status       = "Terbuka";
    $lampiran_sql = $nama_lampiran ? "'$nama_lampiran'" : "NULL";

    $query_insert = "INSERT INTO topik (Id_User, Id_Kategori, Judul_Topik, Isi_Diskusi, Lampiran, Status) 
                     VALUES ('$id_user', '$id_kategori_safe', '$judul_topik', '$isi_safe', $lampiran_sql, '$status')";

    if (mysqli_query($connect, $query_insert)) {
        $_SESSION['pesan_sukses'] = "Topik diskusi baru berhasil diterbitkan!";
        header("Location: riwayat.php");
        exit;
    } else {
        $_SESSION['errors'] = ["database" => "Gagal menyimpan ke database: " . mysqli_error($connect)];
        header("Location: diskusi.php");
        exit;
    }
}

// 2. PROSES EDIT TOPIK CLIENT (POIN 5 CLIENT)
if (isset($_POST['update_topik'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include '../database/koneksi.php';

    if (!isset($_SESSION['Id_User']) && !isset($_SESSION['id_user'])) {
        header("Location: ../auth/masuk.php");
        exit;
    }

    $id_user     = $_SESSION['Id_User'] ?? $_SESSION['id_user'];
    $id_topik    = (int)($_POST['id_topik'] ?? 0);
    $judul_input = trim($_POST['judul'] ?? '');
    $id_kategori = trim($_POST['kategori'] ?? '');
    $isi         = trim($_POST['isi'] ?? '');

    $errors = [];
    $errors = valDiskusi($errors, '', $judul_input, $id_kategori, $isi, false);

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: riwayat.php?edit=" . $id_topik);
        exit;
    }

    $judul_safe       = mysqli_real_escape_string($connect, $judul_input);
    $id_kategori_safe = mysqli_real_escape_string($connect, $id_kategori);
    $isi_safe         = mysqli_real_escape_string($connect, $isi);

    $query_update = "UPDATE topik 
                     SET Judul_Topik = '$judul_safe', Id_Kategori = '$id_kategori_safe', Isi_Diskusi = '$isi_safe' 
                     WHERE Id_Topik = '$id_topik' AND Id_User = '$id_user' AND Status != 'Selesai'";

    if (mysqli_query($connect, $query_update)) {
        $_SESSION['pesan_sukses'] = "Topik diskusi berhasil diperbarui!";
    } else {
        $_SESSION['errors'] = ["database" => "Gagal memperbarui topik: " . mysqli_error($connect)];
    }

    header("Location: riwayat.php");
    exit;
}
?>