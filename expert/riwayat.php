<?php
require_once __DIR__ . "/../validasi.php";

// Cek login dan pastikan role pengguna adalah expert
$user = wajibLogin($connect, "expert");
$id_expert = (int) $user["Id_User"];
$nama_expert = $user["Username"] ?? $user["username"] ?? "Expert";

// Ambil riwayat topik diskusi yang pernah ditanggapi oleh expert yang sedang login
$stmt_topik = mysqli_prepare($connect, "
    SELECT DISTINCT 
        t.*, 
        k.Nama_Kategori, 
        u.Username AS Nama_Pelapor
    FROM topik t
    INNER JOIN respon r ON t.Id_Topik = r.Id_Topik
    LEFT JOIN kategori k ON t.Id_Kategori = k.Id_Kategori
    LEFT JOIN user u ON t.Id_User = u.Id_User
    WHERE r.Id_User = ?
    ORDER BY t.Created_At DESC
");
mysqli_stmt_bind_param($stmt_topik, "i", $id_expert);
mysqli_stmt_execute($stmt_topik);
$query_topik = mysqli_stmt_get_result($stmt_topik);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rembug Rakyat - Riwayat Tanggapan Expert</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR NAVIGASI -->
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

    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-title">
                <a href="../index.php" class="logo logo-dark">
                    <span class="ikon-logo">R</span> Rembug<b>Rakyat</b>
                </a>
            </div>

            <div class="profile" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 14px; font-weight: 600;">
                    <?= htmlspecialchars($nama_expert); ?>
                </span>
                <a href="profil.php" class="avatar" title="Lihat Profil">
                    <?= strtoupper(substr(htmlspecialchars($nama_expert), 0, 1)); ?>
                </a>
            </div>
        </header>

        <section class="content">

            <!-- HEADER HALAMAN & TOMBOL NAVIGASI -->
            <div class="discussion-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Riwayat Tanggapan Saya</h2>
                <a href="diskusi.php" class="btn" style="text-decoration: none;">Lihat Laporan Masuk</a>
            </div>

            <div class="topic-list">
                <?php if ($query_topik && mysqli_num_rows($query_topik) > 0) : ?>
                    <?php while ($topik = mysqli_fetch_assoc($query_topik)) : ?>
                        <?php
                        $id_topik = (int) $topik['Id_Topik'];

                        // Ambil seluruh daftar tanggapan milik expert ini pada topik terkait
                        $stmt_respon = mysqli_prepare($connect, "
                            SELECT r.*, u.Username AS Nama_Expert 
                            FROM respon r
                            LEFT JOIN user u ON r.Id_User = u.Id_User
                            WHERE r.Id_Topik = ? AND r.Id_User = ?
                            ORDER BY r.Id_Respon ASC
                        ");
                        mysqli_stmt_bind_param($stmt_respon, "ii", $id_topik, $id_expert);
                        mysqli_stmt_execute($stmt_respon);
                        $query_respon = mysqli_stmt_get_result($stmt_respon);
                        ?>

                        <!-- KARTU TOPIK RIWAYAT -->
                        <div class="topic-card">
                            <div class="topic-top">
                                <span class="topic-category"><?= htmlspecialchars($topik['Nama_Kategori'] ?? 'Umum'); ?></span>
                                <span class="topic-status sudah-ditanggapi">Sudah Ditanggapi</span>
                            </div>

                            <h3><?= htmlspecialchars($topik['Judul_Topik']); ?></h3>

                            <div class="topic-info">
                                Pelapor: <strong><?= htmlspecialchars($topik['Nama_Pelapor'] ?? 'Masyarakat'); ?></strong> &middot; <?= date('d M Y H:i', strtotime($topik['Created_At'])); ?>
                            </div>

                            <p class="topic-content"><?= nl2br(htmlspecialchars($topik['Isi_Diskusi'])); ?></p>

                            <!-- TAMPILKAN LAMPIRAN FILE JIKA TERSEDIA -->
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

</div>

</body>
</html>