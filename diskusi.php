<?php
session_start();
include "koneksi.php";

// Memeriksa status login
if (!isset($_SESSION['Id_User'])) {
    header("Location: masuk.php");
    exit;
}

$id_user = $_SESSION['Id_User'];
$role = $_SESSION['Role'];
$nama = $_SESSION['Nama_Lengkap'];

$errors = [];
$success = "";

// Mengambil kategori dari database
$query_kategori = mysqli_query(
    $conn,
    "SELECT * FROM Kategori ORDER BY Nama_Kategori ASC"
);

// PROSES FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // CLIENT: Membuat topik baru
    if (isset($_POST['buat_topik']) && $role == 'Client') {

        $jenis_laporan = isset($_POST['jenis_laporan']) ? $_POST['jenis_laporan'] : 'Laporan';
        $judul = trim($_POST['judul']);
        $kategori = (int) $_POST['kategori'];
        $isi = trim($_POST['isi']);

        // Validasi judul
        if ($judul == '') {
            $errors[] = "Judul topik wajib diisi.";
        } elseif (strlen($judul) < 5 || strlen($judul) > 255) {
            $errors[] = "Judul harus terdiri dari 5-255 karakter.";
        }

        // Validasi kategori
        $cek_kategori = mysqli_prepare(
            $conn,
            "SELECT Id_Kategori FROM Kategori WHERE Id_Kategori = ?"
        );
        mysqli_stmt_bind_param($cek_kategori, "i", $kategori);
        mysqli_stmt_execute($cek_kategori);
        $hasil_kategori = mysqli_stmt_get_result($cek_kategori);

        if ($kategori <= 0 || mysqli_num_rows($hasil_kategori) == 0) {
            $errors[] = "Silakan pilih kategori yang tersedia.";
        }

        // Validasi isi diskusi
        if ($isi == '') {
            $errors[] = "Isi laporan wajib diisi.";
        } elseif (strlen($isi) < 10 || strlen($isi) > 10000) {
            $errors[] = "Isi laporan harus terdiri dari 10-10000 karakter.";
        }

        // Menyimpan topik
        if (empty($errors)) {

            $query = "INSERT INTO Topik (Id_User, Id_Kategori, Judul_Topik, Isi_Diskusi, Status) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            $status = "Menunggu Tanggapan";

            mysqli_stmt_bind_param(
                $stmt,
                "iisss",
                $id_user,
                $kategori,
                $judul,
                $isi,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: diskusi.php?berhasil=topik");
                exit;
            } else {
                $errors[] = "Topik gagal disimpan.";
            }
        }
    }

    // EXPERT: Memberikan tanggapan
    if (isset($_POST['balas_topik']) && $role == 'Expert') {

        $id_topik = (int) $_POST['id_topik'];
        $isi_respon = trim($_POST['isi_respon']);

        if ($id_topik <= 0) {
            $errors[] = "Topik tidak valid.";
        }

        if ($isi_respon == '') {
            $errors[] = "Tanggapan wajib diisi.";
        } elseif (strlen($isi_respon) < 5 || strlen($isi_respon) > 10000) {
            $errors[] = "Tanggapan harus terdiri dari 5-10000 karakter.";
        }

        if (empty($errors)) {
            $query = "INSERT INTO Respon (Id_User, Id_Topik, Isi_Respon) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "iis", $id_user, $id_topik, $isi_respon);

            if (mysqli_stmt_execute($stmt)) {
                $update = mysqli_prepare($conn, "UPDATE Topik SET Status = 'Sudah Ditanggapi' WHERE Id_Topik = ?");
                mysqli_stmt_bind_param($update, "i", $id_topik);
                mysqli_stmt_execute($update);

                header("Location: diskusi.php?berhasil=respon");
                exit;
            } else {
                $errors[] = "Tanggapan gagal disimpan.";
            }
        }
    }
}

// Pesan keberhasilan
if (isset($_GET['berhasil'])) {
    if ($_GET['berhasil'] == 'topik') {
        $success = "Aspirasi berhasil dikirim!";
    } elseif ($_GET['berhasil'] == 'respon') {
        $success = "Tanggapan berhasil dikirim!";
    }
}

// Mengambil seluruh topik
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
    <!-- Icon FontAwesome / Google Icons Opsional -->
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
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </a>
            <a href="diskusi.php" class="active">
                <i class="fa-solid fa-file-lines"></i> <span>Laporan</span>
            </a>
            <a href="riwayat.php">
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Laporan</span>
            </a>
        </nav>

        <a href="logout.php" class="logout">
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
                        <p><?php echo aman($error); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>


            <!-- FORM CLIENT (SESUAI MOCKUP) -->
            <?php if ($role == 'Client') { ?>

            <div class="form-container">
                
                <!-- HEADER FORM -->
                <div class="form-header">
                    <div class="header-icon">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div class="header-text">
                        <h1>Form Laporan / Diskusi</h1>
                        <p>Sampaikan aspirasi Anda dengan jelas dan sopan.</p>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data" class="form-body">

                    <!-- JENIS LAPORAN -->
                    <div class="form-group-row">
                        <label class="form-label">Jenis Laporan</label>
                        <div class="form-input radio-group">
                            <label class="radio-label">
                                <input type="radio" name="jenis_laporan" value="Laporan" checked>
                                <span class="radio-custom"></span> Laporan
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="jenis_laporan" value="Diskusi">
                                <span class="radio-custom"></span> Diskusi
                            </label>
                        </div>
                    </div>

                    <!-- JUDUL -->
                    <div class="form-group-row">
                        <label class="form-label">Judul</label>
                        <div class="form-input">
                            <input type="text" name="judul" placeholder="Masukkan judul laporan..." value="<?php echo isset($_POST['judul']) ? aman($_POST['judul']) : ''; ?>" required>
                        </div>
                    </div>

                    <!-- KATEGORI -->
                    <div class="form-group-row">
                        <label class="form-label">Kategori</label>
                        <div class="form-input">
                            <select name="kategori" required>
                                <option value="">Pilih kategori...</option>
                                <?php while ($kategori = mysqli_fetch_assoc($query_kategori)) { ?>
                                    <option value="<?php echo $kategori['Id_Kategori']; ?>" <?php echo (isset($_POST['kategori']) && $_POST['kategori'] == $kategori['Id_Kategori']) ? "selected" : ""; ?>>
                                        <?php echo aman($kategori['Nama_Kategori']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- ISI LAPORAN -->
                    <div class="form-group-row">
                        <label class="form-label">Isi Laporan</label>
                        <div class="form-input">
                            <textarea name="isi" placeholder="Tulis detail laporan atau diskusi Anda di sini..." required><?php echo isset($_POST['isi']) ? aman($_POST['isi']) : ''; ?></textarea>
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

            <?php if (mysqli_num_rows($result_topik) > 0) { ?>
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

                        <!-- TANGGAPAN EXPERT -->
                        <?php
                        $id_topik = $topik['Id_Topik'];
                        $query_respon = "SELECT Respon.*, User.Nama_Lengkap FROM Respon JOIN User ON Respon.Id_User = User.Id_User WHERE Respon.Id_Topik = ? ORDER BY Respon.Created_At ASC";
                        $stmt_respon = mysqli_prepare($conn, $query_respon);
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
                                <form method="POST" class="reply-form">
                                    <input type="hidden" name="id_topik" value="<?php echo $id_topik; ?>">
                                    <textarea name="isi_respon" placeholder="Tuliskan tanggapan resmi..." required></textarea>
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
    // Script Sederhana untuk menampilkan nama file yang di-upload
    const fileInput = document.getElementById('lampiran');
    const fileNameSpan = document.querySelector('.file-name');

    if(fileInput) {
        fileInput.addEventListener('change', function() {
            if(this.files && this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
            } else {
                fileNameSpan.textContent = "Tidak ada file yang dipilih";
            }
        });
    }
</script>

</body>
</html>