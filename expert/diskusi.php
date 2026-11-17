<?php
session_start();
include '../database/koneksi.php';

// Proses simpan tanggapan resmi dari Expert
if (isset($_POST['balas_topik'])) {
    $id_expert  = $_SESSION['Id_User'] ?? $_SESSION['id_user'] ?? 1; // Sesuaikan dengan session login Expert
    $id_topik   = mysqli_real_escape_string($connect, $_POST['id_topik']);
    $isi_respon = trim($_POST['isi_respon'] ?? '');

    if (empty($isi_respon)) {
        echo "<script>alert('Tanggapan tidak boleh kosong!'); window.location.href='diskusi.php';</script>";
        exit;
    }

    // Pastikan topik belum selesai agar tidak ada respon ganda
    $cek_topik = mysqli_query($connect, "SELECT Status FROM topik WHERE Id_Topik = '$id_topik'");
    $data_cek  = mysqli_fetch_assoc($cek_topik);

    if ($data_cek && $data_cek['Status'] !== 'Selesai') {
        $isi_respon_safe = mysqli_real_escape_string($connect, $isi_respon);

        // Simpan ke tabel respon (sesuaikan nama kolom Isi_Respon jika di db Anda berbeda)
        $query_insert = "INSERT INTO respon (Id_Topik, Id_User, Isi_Respon) 
                         VALUES ('$id_topik', '$id_expert', '$isi_respon_safe')";

        if (mysqli_query($connect, $query_insert)) {
            // Otomatis ubah status topik menjadi Selesai agar diskusi ditutup
            mysqli_query($connect, "UPDATE topik SET Status = 'Selesai' WHERE Id_Topik = '$id_topik'");

            echo "<script>
                    alert('Tanggapan resmi berhasil dikirim dan topik telah ditutup!');
                    window.location.href = 'diskusi.php';
                  </script>";
            exit;
        } else {
            echo "<script>alert('Gagal menyimpan tanggapan: " . mysqli_error($connect) . "');</script>";
        }
    }
}

// Ambil semua topik diskusi (urutkan yang belum ditanggapi di bagian paling atas)
$query_topik = mysqli_query($connect, "
    SELECT t.*, k.Nama_Kategori, u.Username 
    FROM topik t
    LEFT JOIN kategori k ON t.Id_Kategori = k.Id_Kategori
    LEFT JOIN user u ON t.Id_User = u.Id_User
    ORDER BY (t.Status = 'Selesai') ASC, t.Created_At DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Panel Expert</title>
    <link rel="stylesheet" href="css/diskusi.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR EXPERT -->
    <aside class="sidebar">
        <a href="beranda.php" class="logo">
            <span class="logo-icon">R</span> Rembug<b>UTM</b>
        </a>

        <nav class="menu">
            <a href="beranda.php">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </a>
            <a href="diskusi.php" class="active">
                <i class="fa-solid fa-comments"></i> <span>Tanggapi Diskusi</span>
            </a>
            <a href="edit_profil.php">
                <i class="fa-solid fa-user-gear"></i> <span>Profil Saya</span>
            </a>
        </nav>

        <a href="../auth/logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i> <span>Keluar</span>
        </a>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-title">
                <a href="beranda.php" class="logo logo-dark">
                    <span class="logo-icon">R</span> Rembug<b>UTM</b>
                </a>
            </div>

            <div class="profile">
                <a href="edit_profil.php" class="avatar">
                    <i class="fa-solid fa-user-shield"></i>
                </a>
            </div>
        </header>

        <section class="content">

            <div class="discussion-heading">
                <h2>Daftar Topik & Aspirasi Masyarakat</h2>
            </div>

            <div class="topic-list">
                <?php if (mysqli_num_rows($query_topik) > 0) : ?>
                    <?php while ($topik = mysqli_fetch_assoc($query_topik)) : ?>
                        <?php
                        $id_topik = $topik['Id_Topik'];

                        // Ambil tanggapan Expert untuk topik ini
                        $query_respon = mysqli_query($connect, "
                            SELECT r.*, u.Username AS Nama_Expert 
                            FROM respon r
                            LEFT JOIN user u ON r.Id_User = u.Id_User
                            WHERE r.Id_Topik = '$id_topik'
                            ORDER BY r.Id_Respon ASC
                        ");
                        $jumlah_respon    = mysqli_num_rows($query_respon);
                        $sudah_ditanggapi = ($jumlah_respon > 0 || $topik['Status'] === 'Selesai');
                        ?>

                        <div class="topic-card">
                            <div class="topic-top">
                                <span class="topic-category"><?= htmlspecialchars($topik['Nama_Kategori'] ?? 'Umum'); ?></span>
                                <?php if ($sudah_ditanggapi) : ?>
                                    <span class="topic-status sudah-ditanggapi">Sudah Ditanggapi (Selesai)</span>
                                <?php else : ?>
                                    <span class="topic-status menunggu-tanggapan">Menunggu Tanggapan</span>
                                <?php endif; ?>
                            </div>

                            <h3><?= htmlspecialchars($topik['Judul_Topik']); ?></h3>

                            <div class="topic-info">
                                Oleh <strong><?= htmlspecialchars($topik['Username'] ?? 'Warga'); ?></strong> · <?= date('d M Y H:i', strtotime($topik['Created_At'])); ?>
                            </div>

                            <p class="topic-content"><?= nl2br(htmlspecialchars($topik['Isi_Diskusi'])); ?></p>

                            <!-- LAMPIRAN JIKA ADA -->
                            <?php if (!empty($topik['Lampiran'])) : ?>
                                <div style="margin: 10px 0;">
                                    <a href="../uploads/<?= htmlspecialchars($topik['Lampiran']); ?>" target="_blank" style="font-size: 13px; color: #2563eb; text-decoration: underline;">
                                        <i class="fa-solid fa-paperclip"></i> Lihat Lampiran (<?= htmlspecialchars($topik['Lampiran']); ?>)
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- AREA TANGGAPAN EXPERT -->
                            <div class="response-area">
                                <h4>Tanggapan Expert</h4>

                                <?php if ($sudah_ditanggapi) : ?>
                                    <?php while ($respon = mysqli_fetch_assoc($query_respon)) : ?>
                                        <div class="response-card">
                                            <div class="expert-name">
                                                <span class="expert-avatar"><i class="fa-solid fa-user-shield"></i></span>
                                                <strong><?= htmlspecialchars($respon['Nama_Expert'] ?? 'Expert'); ?></strong>
                                                <span class="expert-label">Expert</span>
                                            </div>
                                            <p><?= nl2br(htmlspecialchars($respon['Isi_Respon'] ?? $respon['Respon'] ?? '')); ?></p>
                                            <?php if (!empty($respon['Created_At'])) : ?>
                                                <small><?= date('d M Y H:i', strtotime($respon['Created_At'])); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                    <p style="font-size: 13px; color: #64748b; margin-top: 8px;">
                                        <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> Topik ini sudah selesai ditanggapi dan ditutup.
                                    </p>
                                <?php else : ?>
                                    <!-- FORM BALAS MUNCUL HANYA JIKA BELUM DITANGGAPI -->
                                    <form action="diskusi.php" method="POST" class="reply-form">
                                        <input type="hidden" name="id_topik" value="<?= $id_topik; ?>">
                                        <textarea name="isi_respon" placeholder="Tuliskan tanggapan resmi untuk menyelesaikan topik ini..." required></textarea>
                                        <button type="submit" name="balas_topik" class="btn">Kirim Tanggapan & Tutup Topik</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="topic-card" style="text-align: center; padding: 30px;">
                        <p>Belum ada topik diskusi dari masyarakat.</p>
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>

</div>

</body>
</html>