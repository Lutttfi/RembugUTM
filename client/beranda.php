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


// mengambil semua laporan + nama pelapor + kategori + jawaban expert
$query_laporan = mysqli_query($connect, "
    SELECT 
        topik.Judul_Topik,topik.Isi_Diskusi,
        topik.Status,topik.Created_At,
        pelapor.Username AS nama_pelapor,
        kategori.Nama_Kategori,
        respon.Isi_Respon,
        expert.Username AS nama_expert
    FROM topik
    JOIN user AS pelapor ON topik.Id_User = pelapor.Id_User
    JOIN kategori ON topik.Id_Kategori = kategori.Id_Kategori
    JOIN respon ON topik.Id_Topik = respon.Id_Topik
    JOIN user AS expert ON respon.Id_User = expert.Id_User
    ORDER BY topik.Id_Topik DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Beranda</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- wadah paling luar untuk membungkus seluruh isi halaman-->
<div class="bungkus-utama">

    <!-- kotak menu samping sebelah kiri  -->
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

    <!-- wadah utama bagian kanan -->
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

        <!-- wadah isi konten tengah -->
        <div class="isi-halaman">

            <!-- kotak judul halaman "Beranda" dan sapaan selamat datang -->
            <div class="header-beranda">
                <h1>Beranda</h1>
                <p>Selamat Datang <?php echo htmlspecialchars($nama_user); ?>!</p>
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

                            <!-- Cek status nya kalau di database tulisannya 'Selesai' dikasih label hijau-->
                            <?php if ($row["Status"] == "Selesai") { ?>
                                <span class="label-status status-selesai">Selesai</span>
                            <?php }?>
                        
                        </div>

                        <!-- menampilkan laporan yang sudah dibalas sama expert-->
                            <div class="kotak-jawaban">
                                <b>Jawaban Expert (<?= $row["nama_expert"] ?>):</b>
                                <p><?= $row["Isi_Respon"] ?></p>
                            </div>
                        <?php } ?>
                    </div>

                <?php?>
            </div>

            <!-- kotak biru muda paling bawah untuk info/tips -->
            <div class="kotak-tips">
                <!-- pembungkus teks judul dan isi tips -->
                <div class="isi-tips">
                    <h4>Tips</h4>
                    <p>Pastikan laporan Anda jelas dan sesuai dengan kategori yang tersedia.</p>
                </div>
            </div>

        </div>

    </div>

</div>

</body>
</html>