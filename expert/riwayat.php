<?php
<<<<<<< HEAD
require_once __DIR__ . "/../client/validasi.php";

// Cek login + pastikan role expert
$user = wajibLogin($connect, "expert");
$id_expert = (int) $user["Id_User"];

// Ambil riwayat topik/laporan yang pernah ditanggapi oleh expert yang sedang login
$query_topik = mysqli_query($connect, "
    SELECT DISTINCT 
        t.*, 
        k.Nama_Kategori, 
        u.Username AS Nama_Pelapor
    FROM topik t
    INNER JOIN respon r ON t.Id_Topik = r.Id_Topik
    LEFT JOIN kategori k ON t.Id_Kategori = k.Id_Kategori
    LEFT JOIN user u ON t.Id_User = u.Id_User
    WHERE r.Id_User = $id_expert
    ORDER BY t.Created_At DESC
");
?>
=======
session_start();
require "../client/validasi.php";

// mengecek apakah user sudah login
if (!isset($_SESSION["login"])) {
    header("Location: ../auth/masuk.php");
    exit();
}

// mengambil id dan nama expert yang sedang login
$id_user     = $_SESSION["id_user"];
$query_user  = mysqli_query($connect, "SELECT Username FROM user WHERE Id_User = '$id_user'");
$data_user   = mysqli_fetch_assoc($query_user);
$nama_expert = $data_user["Username"] ?? "Expert";

// mengambil riwayat laporan yang sudah dijawab oleh expert yang sedang login
$query_riwayat = mysqli_query($connect, "
    SELECT 
        topik.Judul_Topik, topik.Isi_Diskusi, 
        topik.Status, topik.Created_At AS tgl_laporan,
        pelapor.Username AS nama_pelapor,
        kategori.Nama_Kategori,
        respon.Isi_Respon,
        respon.Created_At AS tgl_respon
    FROM respon
    JOIN topik ON respon.Id_Topik = topik.Id_Topik
    JOIN user AS pelapor ON topik.Id_User = pelapor.Id_User
    JOIN kategori ON topik.Id_Kategori = kategori.Id_Kategori
    WHERE respon.Id_User = '$id_user'
    ORDER BY respon.Id_Respon DESC
");
?>

>>>>>>> 21ca9a5e3c138e74e00886ae4efa529c7020f081
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Rembug Rakyat - Riwayat Tanggapan Expert</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="../index.php" class="logo">
           <span class="ikon-logo">R</span> Rembug<b>Rakyat</b>
        </a>

        <nav class="menu">
            <a href="beranda.php">
                <i class="fa-solid fa-house"></i> <span>Beranda</span>
            </a>
            <a href="diskusi.php">
                <i class="fa-solid fa-comments"></i> <span>Laporan</span>
            </a>
            <a href="riwayat.php" class="active">
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Tanggapan</span>
            </a>
            <a href="profil.php">
                <i class="fa-solid fa-user-gear"></i> <span>Profil Saya</span>
            </a>
        </nav>

        <a href="../auth/logout.php" class="logout">
            <span>Keluar</span>
        </a>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-title">
                <a href="../index.php" class="logo logo-dark">
=======
    <title>RembugRakyat - Riwayat</title>
    <link rel="stylesheet" href="../css/beranda.css">
</head>
<body>

<div class="bungkus-utama">

    <!-- Sidebar kiri -->
    <div class="sidebar">
        <a href="index.php" class="logo">
            <span class="ikon-logo">R</span> Rembug<b>Rakyat</b>
        </a>

        <div class="daftar-menu">
            <a href="beranda.php">
                <span>Beranda</span>
            </a>
            <a href="diskusi.php">
                <span>Laporan</span>
            </a>
            <a href="riwayat.php" class="aktif">
                <span>Riwayat</span>
            </a>
            <a href="profil.php">
                <span>Profil Saya</span>
            </a>
        </div>

        <a href="../auth/logout.php" class="tombol-keluar">
            <span>Keluar</span>
        </a>
    </div>

    <!-- Konten kanan -->
    <div class="konten-kanan">

        <!-- Topbar atas -->
        <div class="topbar">
            <div class="judul-atas">
                <a href="index.php" class="logo logo-gelap">
>>>>>>> 21ca9a5e3c138e74e00886ae4efa529c7020f081
                    <span class="ikon-logo">R</span> Rembug<b>Rakyat</b>
                </a>
            </div>

<<<<<<< HEAD
            <div class="profile" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 14px; font-weight: 600;">
                    <?= htmlspecialchars($user['username'] ?? 'Expert'); ?>
                </span>
                <a href="profil.php" class="avatar" title="Lihat Profil">
                    <?= strtoupper(substr(htmlspecialchars($user['username'] ?? 'E'), 0, 1)); ?>
                </a>
            </div>
        </header>

        <section class="content">

            <!-- HEADER & TOMBOL LIHAT LAPORAN MASUK -->
            <div class="discussion-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Riwayat Tanggapan Saya</h2>
                <a href="diskusi.php" class="btn" style="text-decoration: none;">Lihat Laporan Masuk</a>
            </div>

            <div class="topic-list">
                <?php if ($query_topik && mysqli_num_rows($query_topik) > 0) : ?>
                    <?php while ($topik = mysqli_fetch_assoc($query_topik)) : ?>
                        <?php
                        $id_topik = (int) $topik['Id_Topik'];

                        $query_respon = mysqli_query($connect, "
                            SELECT r.*, u.Username AS Nama_Expert 
                            FROM respon r
                            LEFT JOIN user u ON r.Id_User = u.Id_User
                            WHERE r.Id_Topik = $id_topik AND r.Id_User = $id_expert
                            ORDER BY r.Id_Respon ASC
                        ");
                        ?>

                        <!-- TOPIC CARD DINAMIS -->
                        <div class="topic-card">
                            <div class="topic-top">
                                <span class="topic-category"><?= htmlspecialchars($topik['Nama_Kategori'] ?? 'Umum'); ?></span>
                                <span class="topic-status sudah-ditanggapi">Sudah Ditanggapi</span>
                            </div>

                            <h3><?= htmlspecialchars($topik['Judul_Topik']); ?></h3>

                            <div class="topic-info">
                                Pelapor: <strong><?= htmlspecialchars($topik['Nama_Pelapor'] ?? 'Masyarakat'); ?></strong> · <?= date('d M Y H:i', strtotime($topik['Created_At'])); ?>
                            </div>

                            <p class="topic-content"><?= nl2br(htmlspecialchars($topik['Isi_Diskusi'])); ?></p>

                            <!-- TAMPILKAN LAMPIRAN JIKA ADA (DETEKSI FOLDER OTOMATIS) -->
                            <?php if (!empty($topik['Lampiran'])) : ?>
                                <?php
                                $nama_file = $topik['Lampiran'];
                                $lokasi_cek = [
                                    "../uploads/" . $nama_file,
                                    "../client/uploads/" . $nama_file,
                                    "../assets/uploads/" . $nama_file,
                                    "../img/" . $nama_file,
                                    "../images/" . $nama_file
                                ];

                                $url_lampiran = "../uploads/" . $nama_file;
                                foreach ($lokasi_cek as $path) {
                                    if (file_exists(__DIR__ . "/" . $path)) {
                                        $url_lampiran = $path;
                                        break;
                                    }
                                }
                                ?>
                                <div style="margin-bottom: 16px;">
                                    <a href="<?= htmlspecialchars($url_lampiran); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #2563eb; text-decoration: none; background: #eff6ff; padding: 6px 12px; border-radius: 6px;">
                                        <i class="fa-solid fa-paperclip"></i> Lihat Lampiran (<?= htmlspecialchars($nama_file); ?>)
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- AREA TANGGAPAN EXPERT -->
                            <div class="response-area">
                                <h4>Tanggapan Anda</h4>

                                <?php while ($respon = mysqli_fetch_assoc($query_respon)) : ?>
                                    <div class="response-card">
                                        <div class="expert-name">
                                            <span class="expert-avatar"><i class="fa-solid fa-user-shield"></i></span>
                                            <strong><?= htmlspecialchars($respon['Nama_Expert'] ?? 'Expert'); ?></strong>
                                            <span class="expert-label">Expert</span>
                                        </div>
                                        <p><?= nl2br(htmlspecialchars($respon['Isi_Respon'] ?? '')); ?></p>
                                        <?php if (!empty($respon['Created_At'])) : ?>
                                            <small><?= date('d M Y H:i', strtotime($respon['Created_At'])); ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="topic-card" style="text-align: center; padding: 30px;">
                        <p>Anda belum pernah memberikan tanggapan pada laporan masyarakat.</p>
                        <a href="diskusi.php" class="btn" style="display: inline-block; margin-top: 10px; text-decoration: none;">Tanggapi Laporan Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>
=======
            <div class="profil-user">
                <a href="profil.php" class="foto-profil">
                    Profil
                </a>
            </div>
        </div>

        <!-- Isi halaman -->
        <div class="isi-halaman">

            <div class="header-beranda">
                <h1>Riwayat Tanggapan</h1>
                <p>Daftar laporan mahasiswa yang pernah Anda tanggapi, <?php echo htmlspecialchars($nama_expert); ?>.</p>
            </div>

            <div class="kotak-laporan">
                <div class="kepala-kotak">
                    <h2>Riwayat Jawaban Anda</h2>
                </div>

                <div class="list-item">
                    <?php if (mysqli_num_rows($query_riwayat) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($query_riwayat)) { ?>
                            
                            <div class="item-laporan-lengkap">
                                <div class="atas-laporan">
                                    <div class="teks-laporan">
                                        <h3><?= htmlspecialchars($row["Judul_Topik"]) ?></h3>
                                        
                                        <span class="tanggal">
                                            Oleh <b><?= htmlspecialchars($row["nama_pelapor"]) ?></b> • 
                                            Kategori: <?= htmlspecialchars($row["Nama_Kategori"]) ?> • 
                                            <?= $row["tgl_laporan"] ?>
                                        </span>
                                        
                                        <p class="isi-singkat"><?= htmlspecialchars($row["Isi_Diskusi"]) ?></p>
                                    </div>

                                    <?php if ($row["Status"] == "Selesai") { ?>
                                        <span class="label-status status-selesai">Selesai</span>
                                    <?php } else { ?>
                                        <span class="label-status status-proses">Dalam Proses</span>
                                    <?php } ?>
                                </div>

                                <!-- Kotak jawaban expert -->
                                <div class="kotak-jawaban">
                                    <b>Jawaban Anda (<?= $row["tgl_respon"] ?>):</b><br>
                                    <?= htmlspecialchars($row["Isi_Respon"]) ?>
                                </div>
                            </div>

                        <?php } ?>
                    <?php } else { ?>
                        <p class="tanggal">Belum ada laporan yang Anda tanggapi.</p>
                    <?php } ?>
                </div>
            </div>

        </div>

    </div>
>>>>>>> 21ca9a5e3c138e74e00886ae4efa529c7020f081

</div>

</body>
</html>