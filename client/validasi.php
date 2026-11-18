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

// --- VALIDASI & PROSES FORM DISKUSI ---
function valDiskusi($errors, $jenis, $judul, $kategori, $isi)
{
    if (requiredCheck($jenis) || !in_array($jenis, ['Diskusi', 'Pertanyaan'])) {
        $errors["jenis"] = "Jenis topik wajib dipilih.";
    }

    if (requiredCheck($judul)) {
        $errors["judul"] = "Judul diskusi wajib diisi.";
    } elseif (strlen(trim($judul)) < 5) {
        $errors["judul"] = "Judul diskusi minimal 5 karakter.";
    }

    if (requiredCheck($kategori)) {
        $errors["kategori"] = "Kategori wajib dipilih.";
    }

    if (requiredCheck($isi)) {
        $errors["isi"] = "Isi diskusi wajib diisi.";
    } elseif (strlen(trim($isi)) < 15) {
        $errors["isi"] = "Isi diskusi terlalu singkat, minimal 15 karakter.";
    }

    return $errors;
}

if (isset($_POST['buat_topik'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include '../database/koneksi.php';

    $id_user     = $_SESSION['Id_User'] ?? $_SESSION['id_user'] ?? 1;
    $jenis       = trim($_POST['jenis_laporan'] ?? '');
    $judul_input = trim($_POST['judul'] ?? '');
    $id_kategori = trim($_POST['kategori'] ?? '');
    $isi         = trim($_POST['isi'] ?? '');

    $errors = [];
    $errors = valDiskusi($errors, $jenis, $judul_input, $id_kategori, $isi);

    // Validasi & Upload Lampiran (Opsional)
    $nama_lampiran = NULL;
    if (empty($errors) && isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['lampiran']['tmp_name'];
            $file_name = $_FILES['lampiran']['name'];
            $file_size = $_FILES['lampiran']['size'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
            $max_size    = 5 * 1024 * 1024; // 5MB

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

    // Kembalikan ke diskusi.php jika ada error
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

    // Simpan ke database jika lolos validasi
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
        echo "<script>
                alert('Topik diskusi berhasil diterbitkan!');
                window.location.href = 'riwayat.php';
              </script>";
        exit;
    } else {
        $_SESSION['errors'] = ["database" => "Gagal menyimpan ke database: " . mysqli_error($connect)];
        header("Location: diskusi.php");
        exit;
    }
}
?>