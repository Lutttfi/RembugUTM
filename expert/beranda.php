<?php
session_start();
require "../client/validasi.php";

// Cek apakah user sudah login, kalau belum arahkan ke halaman masuk
if (!isset($_SESSION["login"])) {
    header("Location: ../auth/masuk.php");
    exit();
}

// mengambil nama expert yang sedang login untuk sapaan di atas
$id_user     = $_SESSION["id_user"];
$query_user  = mysqli_query($connect, "SELECT Username FROM user WHERE Id_User = '$id_user'");
$data_user   = mysqli_fetch_assoc($query_user);
$nama_expert = $data_user["Username"] ?? "Expert";

// mengambil semua kiriman laporan dari client dengan menggabungkan tabel topik, user, dan kategori
$query_laporan = mysqli_query($connect, "
    SELECT topik.Judul_Topik, topik.Isi_Diskusi,
        topik.Status,topik.Created_At,
        user.Username AS nama_pelapor,
        kategori.Nama_Kategori
    FROM topik JOIN user ON topik.Id_User = user.Id_User
    JOIN kategori ON topik.Id_Kategori = kategori.Id_Kategori
    ORDER BY topik.Id_Topik DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Beranda</title>
    <!-- ditambah ?v=2 biar browser langsung muat CSS yang terbaru -->
    <link rel="stylesheet" href="css/beranda.css?v=3">
</head>
<body>

<!-- wadah paling luar untuk membungkus seluruh isi halaman (kiri & kanan) -->
<div class="bungkus-utama">

    <!-- kotak menu samping sebelah kiri yang posisinya diam/tetap -->
    <div class="sidebar">
        <a href="index.php" class="logo">
            <span class="ikon-logo">R</span> Rembug<b>UTM</b>
        </a>

        <!-- wadah untuk menyusun daftar tombol menu secara menurun ke bawah -->
        <div class="daftar-menu">
            <a href="beranda.php" class="aktif">
                <span>Beranda</span>
            </a>
            <a href="diskusi.php">
                <span>Laporan</span>
            </a>
            <!-- <a href="riwayat.php">
                <span>Riwayat Laporan</span>
            </a> -->
            <a href="profil.php">
                <span>Profil Saya</span>
            </a>
        </div>

        <a href="../auth/logout.php" class="tombol-keluar">
            <span>Keluar</span>
        </a>
    </div>

    <!-- wadah utama bagian kanan (dikasih jarak kiri 260px biar tidak ketutup sidebar) -->
    <div class="konten-kanan">

        <!-- bar putih paling atas untuk judul dan tombol profil -->
        <div class="topbar">
            <!-- kotak bagian kiri di topbar (tempat logo) -->
            <div class="judul-atas">
                <a href="index.php" class="logo logo-gelap">
                    <span class="ikon-logo">R</span> Rembug<b>UTM</b>
                </a>
            </div>

            <!-- kotak bagian kanan di topbar (tempat tombol profil) -->
            <div class="profil-user">
                <a href="profil.php" class="foto-profil">
                    Profil
                </a>
            </div>
        </div>

        <!-- wadah isi konten tengah biar lebarnya rapi dan ada jarak pinggir -->
        <div class="isi-halaman">

            <!-- kotak khusus judul halaman "Beranda - Expert" dan keterangan -->
            <div class="header-beranda">
                <h1>Beranda - Expert</h1>
                <p>Selamat Datang <?php echo htmlspecialchars($nama_expert); ?>! Kelola laporan dan tanggapi aspirasi mahasiswa.</p>
            </div>

            <!-- kotak putih besar untuk bagian daftar laporan terbaru -->
            <div class="kotak-laporan">
                <!-- bagian atas kotak laporan (memisahkan judul di kiri & link "Lihat Semua" di kanan) -->
                <div class="kepala-kotak">
                    <h2>Laporan Terbaru</h2>
                </div>

                <!-- wadah kumpulan baris laporan -->
                <div class="list-item">
                 <!-- Ambil semua kiriman laporan client satu per satu dari database -->
                <?php while ($row = mysqli_fetch_assoc($query_laporan)) { ?>
                    
                    <div class="item-laporan-lengkap">
                        <div class="atas-laporan">
                            <div class="teks-laporan">
                                <!-- Judul laporan dari client -->
                                <h3><?= $row["Judul_Topik"] ?></h3>
                                
                                <!-- Info nama client yang melapor, kategori, dan tanggal -->
                                <span class="tanggal">
                                    Oleh <b><?= $row["nama_pelapor"] ?></b> • 
                                    Kategori: <?= $row["Nama_Kategori"] ?> • 
                                    <?= $row["Created_At"] ?>
                                </span>
                                
                                <!-- Isi keluhan/aspirasi dari client -->
                                <p class="isi-singkat"><?= $row["Isi_Diskusi"] ?></p>
                            </div>

                            <!-- Label status laporan -->
                            <?php if ($row["Status"] == "Selesai") { ?>
                                <span class="label-status status-selesai">Selesai</span>
                            <?php } else { ?>
                                <span class="label-status status-proses">Dalam Proses</span>
                            <?php } ?>
                        </div>
                    </div>

                <?php } ?>
            </div>
            </div>

        </div>

    </div>

</div>

</body>
</html>