<?php
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <span class="ikon-logo">R</span> Rembug<b>Rakyat</b>
                </a>
            </div>

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

</div>

</body>
</html>