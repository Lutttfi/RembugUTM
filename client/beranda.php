<?php
session_start();
require "../client/validasi.php";

// Cek login
if (!isset($_SESSION["login"])) {
    header("Location: ../auth/masuk.php");
    exit();
}

// mengambil nama user yang login
$id_user    = $_SESSION["id_user"];
$query_user = mysqli_query($connect, "SELECT Username FROM user WHERE Id_User = '$id_user'");
$data_user  = mysqli_fetch_assoc($query_user);
$nama_user  = $data_user["Username"] ?? "Mahasiswa";

// menghitung total laporan
$q_total       = mysqli_query($connect, "SELECT COUNT(*) AS total FROM topik");
$total_laporan = mysqli_fetch_assoc($q_total)["total"];
// menghitung total yang sedang di proses
$q_proses      = mysqli_query($connect, "SELECT COUNT(*) AS total FROM topik WHERE Status = 'Terbuka'");
$total_proses  = mysqli_fetch_assoc($q_proses)["total"];
// menghitung total laporan yang sudah selesai
$q_selesai     = mysqli_query($connect, "SELECT COUNT(*) AS total FROM topik WHERE Status = 'Selesai'");
$total_selesai = mysqli_fetch_assoc($q_selesai)["total"];

// mengambil semua laporan + nama pelapor + kategori + jawaban expert
$query_laporan = mysqli_query($connect, "
    SELECT 
        topik.Judul_Topik,
        topik.Isi_Diskusi,
        topik.Status,
        topik.Created_At,
        pelapor.Username AS nama_pelapor,
        kategori.Nama_Kategori,
        respon.Isi_Respon,
        expert.Username AS nama_expert
    FROM topik
    JOIN user AS pelapor ON topik.Id_User = pelapor.Id_User
    JOIN kategori ON topik.Id_Kategori = kategori.Id_Kategori
    LEFT JOIN respon ON topik.Id_Topik = respon.Id_Topik
    LEFT JOIN user AS expert ON respon.Id_User = expert.Id_User
    ORDER BY topik.Id_Topik DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Beranda</title>
    <link rel="stylesheet" href="css/style.css?v=3">
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
            <a href="riwayat.php">
                <span>Riwayat Laporan</span>
            </a>
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

        <!-- wadah isi konten tengah biar lebarnya rapi (maksimal 1000px) dan ada jarak pinggir -->
        <div class="isi-halaman">

            <!-- kotak khusus judul halaman "Beranda" dan sapaan selamat datang -->
            <div class="header-beranda">
                <h1>Beranda</h1>
                <p>Selamat Datang <?php echo htmlspecialchars($nama_user); ?>!</p>
            </div>


                <!-- kotak pertama yang berisi total seluruh laporan -->
                <div class="baris-statistik">
                <div class="kotak-stat">
                    <div class="info-stat">
                        <h2><?php echo $total_laporan; ?></h2>
                        <span>Total Laporan</span>
                    </div>
                </div>
                <!-- kotak kedua yang berisi laporan sedang dalam proses -->
                <div class="kotak-stat">
                    <div class="info-stat">
                        <h2><?php echo $total_proses; ?></h2>
                        <span>Dalam Proses</span>
                    </div>
                </div>
                <!-- kotak ketiga yang berisi bahwa laporan sudah selesai -->
                <div class="kotak-stat">
                    <div class="info-stat">
                        <h2><?php echo $total_selesai; ?></h2>
                        <span>Selesai</span>
                    </div>
                </div>
            </div>
            </div>

            <!-- kotak putih besar untuk bagian daftar laporan terbaru -->
            <div class="kotak-laporan">
                
            <div class="list-item">
                <!-- Ambil semua data laporan satu per satu dari database, lalu cetak ke layar -->
                <?php while ($row = mysqli_fetch_assoc($query_laporan)) { ?>
                    
                    <div class="item-laporan-lengkap">
                        <div class="atas-laporan">
                            <div class="teks-laporan">
                                <!-- Tampilkan judul laporannya -->
                                <h3><?= $row["Judul_Topik"] ?></h3>
                                
                                <!-- Tampilkan info siapa yang lapor, kategorinya apa, dan kapan dibuat -->
                                <span class="tanggal">
                                    Oleh <b><?= $row["nama_pelapor"] ?></b> • 
                                    Kategori: <?= $row["Nama_Kategori"] ?> • 
                                    <?= $row["Created_At"] ?>
                                </span>
                                
                                <!-- Tampilkan isi cerita/keluhan laporannya -->
                                <p class="isi-singkat"><?= $row["Isi_Diskusi"] ?></p>
                            </div>

                            <!-- Cek status nya kalau di database tulisannya 'Selesai', kasih label hijau. Kalau belum, kasih label biru 'Dalam Proses' -->
                            <?php if ($row["Status"] == "Selesai") { ?>
                                <span class="label-status status-selesai">Selesai</span>
                            <?php } else { ?>
                                <span class="label-status status-proses">Dalam Proses</span>
                            <?php } ?>
                        </div>

                        <!-- Cek apakah laporan ini sudah dibalas sama expert? Kalau balasannya nggak kosong, munculkan kotak biru jawabannya -->
                        <?php if ($row["Isi_Respon"] != "") { ?>
                            <div class="kotak-jawaban">
                                <b>Jawaban Expert (<?= $row["nama_expert"] ?>):</b>
                                <p><?= $row["Isi_Respon"] ?></p>
                            </div>
                        <?php } ?>
                    </div>

                <?php } ?>
            </div>

            <!-- kotak biru muda paling bawah untuk info/tips -->
            <div class="kotak-tips">
                <!-- pembungkus teks judul dan isi tips -->
                <div class="isi-tips">
                    <h4>Tips</h4>
                    <p>Pastikan laporan Anda jelas dan sesuai dengan kategori yang tersedia.</p>
                </div>
            </div>

            <!-- <br>
            <hr style="border: 1px dashed #c5d3ea; margin: 30px 0;"> -->

        </div>

    </div>

</div>

</body>
</html>