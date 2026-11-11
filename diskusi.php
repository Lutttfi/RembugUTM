<?php
// Mengabaikan session login untuk kebutuhan pengujian layout & form
include "koneksi.php";

// SIMULASI AKUN (Tanpa perlu login / register terlebih dahulu)
// Anda bisa mengubah $role menjadi 'Expert' jika ingin menguji tampilan/fitur Expert
$id_user = 1; 
$role    = 'Client'; // Opsi: 'Client' atau 'Expert'
$nama    = 'Ahmad Fauzi';

$errors  = [];
$success = "";

// Mengambil data kategori dari database
$query_kategori = mysqli_query($conn, "SELECT * FROM Kategori ORDER BY Nama_Kategori ASC");

// PROSES FORM (SERVER-SIDE VALIDATION SISI PHP)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // CLIENT: Membuat topik baru
    if (isset($_POST['buat_topik']) && $role == 'Client') {

        $jenis_laporan = isset($_POST['jenis_laporan']) ? trim($_POST['jenis_laporan']) : 'Laporan';
        $judul         = isset($_POST['judul']) ? trim($_POST['judul']) : '';
        $kategori_raw  = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
        $isi           = isset($_POST['isi']) ? trim($_POST['isi']) : '';

        // 1. VALIDASI MASUKAN WAJIB (Required Validation)
        if ($judul === '') {
            $errors[] = "Judul topik wajib diisi.";
        }
        if ($kategori_raw === '') {
            $errors[] = "Kategori wajib dipilih.";
        }
        if ($isi === '') {
            $errors[] = "Isi laporan wajib diisi.";
        }

        // 2. VALIDASI FORMAT NUMERIK (Numeric Validation)
        if ($kategori_raw !== '' && !ctype_digit($kategori_raw)) {
            $errors[] = "ID Kategori harus berupa format angka/numerik yang valid.";
        } else {
            $kategori = (int)$kategori_raw;
        }

        // 3. VALIDASI PANJANG KARAKTER MINIMUM & MAKSIMUM
        if ($judul !== '') {
            if (strlen($judul) < 5) {
                $errors[] = "Judul terlalu pendek, minimal 5 karakter.";
            } elseif (strlen($judul) > 255) {
                $errors[] = "Judul terlalu panjang, maksimal 255 karakter.";
            }
        }

        if ($isi !== '') {
            if (strlen($isi) < 10) {
                $errors[] = "Isi laporan terlalu pendek, minimal 10 karakter.";
            } elseif (strlen($isi) > 10000) {
                $errors[] = "Isi laporan terlalu panjang, maksimal 10.000 karakter.";
            }
        }

        // 4. VALIDASI EKSISTENSI KATEGORI DI DATABASE
        if (empty($errors) && isset($kategori)) {
            $cek_kategori = mysqli_prepare($conn, "SELECT Id_Kategori FROM Kategori WHERE Id_Kategori = ?");
            mysqli_stmt_bind_param($cek_kategori, "i", $kategori);
            mysqli_stmt_execute($cek_kategori);
            $hasil_kategori = mysqli_stmt_get_result($cek_kategori);

            if (mysqli_num_rows($hasil_kategori) == 0) {
                $errors[] = "Kategori yang Anda pilih tidak terdaftar di sistem.";
            }
        }

        // 5. VALIDASI FILE / LAMPIRAN
        $nama_file_db = null;
        if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == UPLOAD_ERR_OK) {
            $file_name = $_FILES['lampiran']['name'];
            $file_size = $_FILES['lampiran']['size'];
            $file_tmp  = $_FILES['lampiran']['tmp_name'];
            
            $ext_allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $file_ext    = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $ext_allowed)) {
                $errors[] = "Format lampiran tidak valid. Hanya diperbolehkan: JPG, PNG, PDF.";
            }

            if ($file_size > 5 * 1024 * 1024) { // Maksimal 5MB
                $errors[] = "Ukuran lampiran terlalu besar, maksimal 5 MB.";
            }

            if (empty($errors)) {
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                $nama_file_db = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
                move_uploaded_file($file_tmp, "uploads/" . $nama_file_db);
            }
        }

        // PROSES SIMPAN
        if (empty($errors)) {
            $query  = "INSERT INTO Topik (Id_User, Id_Kategori, Judul_Topik, Isi_Diskusi, Lampiran, Status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt   = mysqli_prepare($conn, $query);
            $status = "Menunggu Tanggapan";

            mysqli_stmt_bind_param($stmt, "iissss", $id_user, $kategori, $judul, $isi, $nama_file_db, $status);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Aspirasi berhasil dikirim!";
                $_POST = []; // Reset input form setelah berhasil
            } else {
                $errors[] = "Gagal menyimpan data ke database.";
            }
        }
    }

    // EXPERT: Memberikan tanggapan
    if (isset($_POST['balas_topik']) && $role == 'Expert') {
        $id_topik_raw = isset($_POST['id_topik']) ? trim($_POST['id_topik']) : '';
        $isi_respon   = isset($_POST['isi_respon']) ? trim($_POST['isi_respon']) : '';

        if ($isi_respon === '') {
            $errors[] = "Tanggapan resmi wajib diisi.";
        } elseif (strlen($isi_respon) < 5 || strlen($isi_respon) > 10000) {
            $errors[] = "Tanggapan harus terdiri dari 5 - 10.000 karakter.";
        }

        if (empty($errors)) {
            $id_topik = (int)$id_topik_raw;
            $query    = "INSERT INTO Respon (Id_User, Id_Topik, Isi_Respon) VALUES (?, ?, ?)";
            $stmt     = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "iis", $id_user, $id_topik, $isi_respon);

            if (mysqli_stmt_execute($stmt)) {
                $update = mysqli_prepare($conn, "UPDATE Topik SET Status = 'Sudah Ditanggapi' WHERE Id_Topik = ?");
                mysqli_stmt_bind_param($update, "i", $id_topik);
                mysqli_stmt_execute($update);

                $success = "Tanggapan berhasil dikirim!";
            } else {
                $errors[] = "Tanggapan gagal disimpan.";
            }
        }
    }
}

// Fetch seluruh data topik
$query_topik = "
    SELECT Topik.*, User.Nama_Lengkap, Kategori.Nama_Kategori
    FROM Topik
    JOIN User ON Topik.Id_User = User.Id_User
    JOIN Kategori ON Topik.Id_Kategori = Kategori.Id_Kategori
    ORDER BY Topik.Created_At DESC
";
$result_topik = mysqli_query($conn, $query_topik);

function aman($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirasi Kampus - Form Laporan</title>
    <link rel="stylesheet" href="diskusi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <i class="fa-solid fa-comments"></i>
            </div>
            <h2>Aspirasi Kampus</h2>
        </div>

        <nav class="menu">
            <a href="#">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </a>
            <a href="diskusi.php" class="active">
                <i class="fa-solid fa-file-lines"></i> <span>Laporan</span>
            </a>
            <a href="#">
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Laporan</span>
            </a>
        </nav>

        <a href="#" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i> <span>Keluar</span>
        </a>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-title">
                <i class="fa-solid fa-comments"></i>
                <h2>Aspirasi Kampus</h2>
            </div>

            <div class="profile">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </header>

        <section class="content">

            <!-- NOTIFIKASI SUKSES / ERROR -->
            <?php if ($success != '') { ?>
                <div class="alert success"><?php echo aman($success); ?></div>
            <?php } ?>

            <?php if (!empty($errors)) { ?>
                <div class="alert error">
                    <?php foreach ($errors as $error) { ?>
                        <p><i class="fa-solid fa-circle-exclamation"></i> <?php echo aman($error); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>

            <!-- FORM CLIENT -->
            <?php if ($role == 'Client') { ?>
            <div class="form-container">
                <div class="form-header">
                    <div class="header-icon">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div class="header-text">
                        <h1>Form Laporan / Diskusi</h1>
                        <p>Sampaikan aspirasi Anda dengan jelas dan sopan.</p>
                    </div>
                </div>

                <!-- Form dikirim ke halaman yang sama (diskusi.php) tanpa HTML validation attributes -->
                <form method="POST" action="diskusi.php" enctype="multipart/form-data" class="form-body" novalidate>

                    <!-- JENIS LAPORAN -->
                    <div class="form-group-row">
                        <label class="form-label">Jenis Laporan</label>
                        <div class="form-input radio-group">
                            <label class="radio-label">
                                <input type="radio" name="jenis_laporan" value="Laporan" <?php echo (!isset($_POST['jenis_laporan']) || $_POST['jenis_laporan'] == 'Laporan') ? 'checked' : ''; ?>>
                                Laporan
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="jenis_laporan" value="Diskusi" <?php echo (isset($_POST['jenis_laporan']) && $_POST['jenis_laporan'] == 'Diskusi') ? 'checked' : ''; ?>>
                                Diskusi
                            </label>
                        </div>
                    </div>

                    <!-- JUDUL -->
                    <div class="form-group-row">
                        <label class="form-label">Judul</label>
                        <div class="form-input">
                            <input type="text" name="judul" placeholder="Masukkan judul laporan..." value="<?php echo isset($_POST['judul']) ? aman($_POST['judul']) : ''; ?>">
                        </div>
                    </div>

                    <!-- KATEGORI -->
                    <div class="form-group-row">
                        <label class="form-label">Kategori</label>
                        <div class="form-input">
                            <select name="kategori">
                                <option value="">Pilih kategori...</option>
                                <?php 
                                if ($query_kategori && mysqli_num_rows($query_kategori) > 0) {
                                    mysqli_data_seek($query_kategori, 0);
                                    while ($kat = mysqli_fetch_assoc($query_kategori)) { 
                                ?>
                                    <option value="<?php echo $kat['Id_Kategori']; ?>" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] == $kat['Id_Kategori']) ? "selected" : ""; ?>>
                                        <?php echo aman($kat['Nama_Kategori']); ?>
                                    </option>
                                <?php 
                                    }
                                } 
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- ISI LAPORAN -->
                    <div class="form-group-row">
                        <label class="form-label">Isi Laporan</label>
                        <div class="form-input">
                            <textarea name="isi" placeholder="Tulis detail laporan atau diskusi Anda di sini..."><?php echo isset($_POST['isi']) ? aman($_POST['isi']) : ''; ?></textarea>
                        </div>
                    </div>

                    <!-- LAMPIRAN -->
                    <div class="form-group-row">
                        <label class="form-label">Lampiran <small class="text-muted">(Opsional)</small></label>
                        <div class="form-input">
                            <div class="file-upload-box">
                                <input type="file" name="lampiran" id="lampiran" class="file-input">
                                <label for="lampiran" class="btn-choose-file">Choose File</label>
                                <span class="file-name">Tidak ada file yang dipilih</span>
                            </div>
                            <span class="input-hint">Format: JPG, PNG, PDF (maks. 5MB)</span>
                        </div>
                    </div>

                    <!-- FOOTER BUTTONS -->
                    <div class="form-footer-actions">
                        <button type="reset" class="btn-cancel">Batal</button>
                        <button type="submit" name="buat_topik" class="btn-submit">Kirim Laporan</button>
                    </div>

                </form>
            </div>
            <?php } ?>

            <!-- LIST TOPIK & DISKUSI -->
            <div class="discussion-heading">
                <h2>Daftar Laporan & Diskusi</h2>
            </div>

            <?php if ($result_topik && mysqli_num_rows($result_topik) > 0) { ?>
                <?php while ($topik = mysqli_fetch_assoc($result_topik)) { ?>
                    <div class="topic-card">
                        <div class="topic-top">
                            <span class="topic-category"><?php echo aman($topik['Nama_Kategori']); ?></span>
                            <span class="topic-status <?php echo strtolower(str_replace(' ', '-', $topik['Status'])); ?>">
                                <?php echo aman($topik['Status']); ?>
                            </span>
                        </div>

                        <h3><?php echo aman($topik['Judul_Topik']); ?></h3>

                        <div class="topic-info">
                            Oleh <strong><?php echo aman($topik['Nama_Lengkap']); ?></strong> · <?php echo date('d M Y H:i', strtotime($topik['Created_At'])); ?>
                        </div>

                        <p class="topic-content"><?php echo nl2br(aman($topik['Isi_Diskusi'])); ?></p>

                        <?php if (!empty($topik['Lampiran'])) { ?>
                            <div class="topic-attachment">
                                <i class="fa-solid fa-paperclip"></i> Lampiran: 
                                <a href="uploads/<?php echo aman($topik['Lampiran']); ?>" target="_blank"><?php echo aman($topik['Lampiran']); ?></a>
                            </div>
                        <?php } ?>

                        <!-- TANGGAPAN EXPERT -->
                        <?php
                        $id_topik = $topik['Id_Topik'];
                        $query_respon = "SELECT Respon.*, User.Nama_Lengkap FROM Respon JOIN User ON Respon.Id_User = User.Id_User WHERE Respon.Id_Topik = ? ORDER BY Respon.Created_At ASC";
                        $stmt_respon  = mysqli_prepare($conn, $query_respon);
                        mysqli_stmt_bind_param($stmt_respon, "i", $id_topik);
                        mysqli_stmt_execute($stmt_respon);
                        $result_respon = mysqli_stmt_get_result($stmt_respon);
                        ?>

                        <div class="response-area">
                            <h4>Tanggapan Expert</h4>
                            <?php if (mysqli_num_rows($result_respon) > 0) { ?>
                                <?php while ($respon = mysqli_fetch_assoc($result_respon)) { ?>
                                    <div class="response-card">
                                        <div class="expert-name">
                                            <span class="expert-avatar"><i class="fa-solid fa-user-shield"></i></span>
                                            <strong><?php echo aman($respon['Nama_Lengkap']); ?></strong>
                                            <span class="expert-label">Expert</span>
                                        </div>
                                        <p><?php echo nl2br(aman($respon['Isi_Respon'])); ?></p>
                                        <small><?php echo date('d M Y H:i', strtotime($respon['Created_At'])); ?></small>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <p class="empty-response">Belum ada tanggapan dari Expert.</p>
                            <?php } ?>

                            <?php if ($role == 'Expert') { ?>
                                <form method="POST" action="diskusi.php" class="reply-form" novalidate>
                                    <input type="hidden" name="id_topik" value="<?php echo $id_topik; ?>">
                                    <textarea name="isi_respon" placeholder="Tuliskan tanggapan resmi..."></textarea>
                                    <button type="submit" name="balas_topik" class="btn-submit">Kirim Tanggapan</button>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="empty-data">
                    <h3>Belum Ada Laporan</h3>
                    <p>Laporan yang kamu buat akan tampil di sini.</p>
                </div>
            <?php } ?>

        </section>

    </main>

</div>

<script>
    const fileInput = document.getElementById('lampiran');
    const fileNameSpan = document.querySelector('.file-name');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
            } else {
                fileNameSpan.textContent = "Tidak ada file yang dipilih";
            }
        });
    }
</script>

</body>
</html>