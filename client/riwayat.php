<?php
session_start();
include '../database/koneksi.php';

// Ambil semua topik diskusi beserta nama pembuat dan kategorinya dari database
$query_topik = mysqli_query($connect, "
    SELECT t.*, k.Nama_Kategori, u.Username 
    FROM topik t
    LEFT JOIN kategori k ON t.Id_Kategori = k.Id_Kategori
    LEFT JOIN user u ON t.Id_User = u.Id_User
    ORDER BY t.Created_At DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Riwayat Diskusi</title>
    <link rel="stylesheet" href="css/diskusi.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="index.php" class="logo">
            <span class="logo-icon">R</span> Rembug<b>UTM</b>
        </a>

        <nav class="menu">
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </a>
            <a href="diskusi.php">
                <i class="fa-solid fa-comments"></i> <span>Forum Diskusi</span>
            </a>
            <a href="riwayat.php" class="active">
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Diskusi</span>
            </a>
            <a href="profil.php">
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
                <a href="index.php" class="logo logo-dark">
                    <span class="logo-icon">R</span> Rembug<b>UTM</b>
                </a>
            </div>

            <div class="profile">
                <a href="profil.php" class="avatar">
                    <i class="fa-solid fa-user"></i>
                </a>
            </div>
        </header>

        <section class="content">

            <!-- HEADER & TOMBOL BUAT TOPIK BARU -->
            <div class="discussion-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Riwayat Laporan & Diskusi</h2>
                <a href="diskusi.php" class="btn" style="text-decoration: none;">+ Buat Topik Baru</a>
            </div>

            <div class="topic-list">
                <?php if (mysqli_num_rows($query_topik) > 0) : ?>
                    <?php while ($topik = mysqli_fetch_assoc($query_topik)) : ?>
                        <?php
                        $id_topik = $topik['Id_Topik'];
                        
                        // Ambil tanggapan Expert untuk topik ini dari tabel respon
                        $query_respon = mysqli_query($connect, "
                            SELECT r.*, u.Username AS Nama_Expert 
                            FROM respon r
                            LEFT JOIN user u ON r.Id_User = u.Id_User
                            WHERE r.Id_Topik = '$id_topik'
                            ORDER BY r.Id_Respon ASC
                        ");
                        $jumlah_respon = mysqli_num_rows($query_respon);
                        $sudah_ditanggapi = ($jumlah_respon > 0 || $topik['Status'] === 'Selesai');
                        ?>

                        <!-- TOPIC CARD DINAMIS -->
                        <div class="topic-card">
                            <div class="topic-top">
                                <span class="topic-category"><?= htmlspecialchars($topik['Nama_Kategori'] ?? 'Umum'); ?></span>
                                <?php if ($sudah_ditanggapi) : ?>
                                    <span class="topic-status sudah-ditanggapi">Sudah Ditanggapi</span>
                                <?php else : ?>
                                    <span class="topic-status menunggu-tanggapan">Menunggu Tanggapan</span>
                                <?php endif; ?>
                            </div>

                            <h3><?= htmlspecialchars($topik['Judul_Topik']); ?></h3>

                            <div class="topic-info">
                                Oleh <strong><?= htmlspecialchars($topik['Username'] ?? 'Warga'); ?></strong> · <?= date('d M Y H:i', strtotime($topik['Created_At'])); ?>
                            </div>

                            <p class="topic-content"><?= nl2br(htmlspecialchars($topik['Isi_Diskusi'])); ?></p>

                            <!-- TAMPILKAN LAMPIRAN JIKA ADA -->
                            <?php if (!empty($topik['Lampiran'])) : ?>
                                <div style="margin: 10px 0;">
                                    <a href="../uploads/<?= htmlspecialchars($topik['Lampiran']); ?>" target="_blank" style="font-size: 13px; color: #2563eb; text-decoration: underline;">
                                        <i class="fa-solid fa-paperclip"></i> Lihat Lampiran (<?= htmlspecialchars($topik['Lampiran']); ?>)
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- AREA TANGGAPAN EXPERT (TANPA FORM REPLY) -->
                            <div class="response-area">
                                <h4>Tanggapan Expert</h4>

                                <?php if ($jumlah_respon > 0) : ?>
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
                                    
                                    <p style="font-size: 13px; color: #64748b; margin-top: 10px;">
                                        Topik ini telah ditanggapi dan ditutup. Punya pembahasan lain? <a href="diskusi.php" style="color: #2563eb; font-weight: 600;">Buat Topik Baru</a>.
                                    </p>
                                <?php else : ?>
                                    <p class="empty-response">Belum ada tanggapan dari Expert.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="topic-card" style="text-align: center; padding: 30px;">
                        <p>Belum ada topik diskusi yang dibuat.</p>
                        <a href="diskusi.php" class="btn" style="display: inline-block; margin-top: 10px; text-decoration: none;">Mulai Topik Pertama</a>
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>

</div>

</body>
</html>