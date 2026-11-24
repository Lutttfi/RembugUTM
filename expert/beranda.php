<?php
session_start();
require "../client/validasi.php";

// CEK LOGIN: Kalau belum login, paksa pindah ke halaman masuk.php
if (!isset($_SESSION["login"])) {
    header("Location: ../auth/masuk.php"); // sesuaikan letak folder masuk.php kamu
    exit();
}

// Ambil username dari database berdasarkan id_user yang login
$id_user = $_SESSION["id_user"];
$query   = mysqli_query($connect, "SELECT username FROM user WHERE Id_User = '$id_user'");
$data    = mysqli_fetch_assoc($query);

$nama_expert = $data["username"] ?? "Expert";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Beranda</title>
    <!-- ditambah ?v=2 biar browser langsung muat CSS yang terbaru -->
    <link rel="stylesheet" href="css/beranda.css?v=2">
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

            <!-- wadah baris untuk menjajarkan 3 kotak angka statistik ke samping -->
            <div class="baris-statistik">
                <!-- kotak putih pertama: total laporan -->
                <div class="kotak-stat">
                    <!-- pembungkus teks angka dan keterangannya -->
                    <div class="info-stat">
                        <h2>5</h2>
                        <span>Total Laporan</span>
                    </div>
                </div>

                <!-- kotak putih kedua: laporan dalam proses -->
                <div class="kotak-stat">
                    <div class="info-stat">
                        <h2>3</h2>
                        <span>Dalam Proses</span>
                    </div>
                </div>

                <!-- kotak putih ketiga: laporan selesai -->
                <div class="kotak-stat">
                    <div class="info-stat">
                        <h2>2</h2>
                        <span>Selesai</span>
                    </div>
                </div>
            </div>

            <!-- kotak putih besar untuk bagian daftar laporan terbaru -->
            <div class="kotak-laporan">
                <!-- bagian atas kotak laporan (memisahkan judul di kiri & link "Lihat Semua" di kanan) -->
                <div class="kepala-kotak">
                    <h2>Laporan Terbaru</h2>
                    <a href="diskusi.php" class="lihat-semua">Lihat Semua</a>
                </div>

                <!-- wadah kumpulan baris laporan -->
                <div class="list-item">
                    
                    <!-- baris laporan ke-1 -->
                    <div class="item-laporan">
                        <!-- pembungkus sebelah kiri khusus judul laporan dan tanggal -->
                        <div class="bagian-kiri">
                            <!-- kotak teks judul dan tanggal biar tersusun atas-bawah -->
                            <div class="teks-laporan">
                                <h3>Permintaan penambahan ruang belajar</h3>
                                <span class="tanggal">10 Nov 2026</span>
                            </div>
                        </div>
                        <span class="label-status status-proses">Dalam Proses</span>
                    </div>

                    <!-- baris laporan ke-2 -->
                    <div class="item-laporan">
                        <div class="bagian-kiri">
                            <div class="teks-laporan">
                                <h3>Keluhan fasilitas AC</h3>
                                <span class="tanggal">09 Nov 2026</span>
                            </div>
                        </div>
                        <span class="label-status status-baru">Baru</span>
                    </div>

                    <!-- baris laporan ke-3 -->
                    <div class="item-laporan">
                        <div class="bagian-kiri">
                            <div class="teks-laporan">
                                <h3>Saran peningkatan WiFi</h3>
                                <span class="tanggal">08 Nov 2026</span>
                            </div>
                        </div>
                        <span class="label-status status-selesai">Selesai</span>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

</body>
</html>