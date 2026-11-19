<?php
session_start();
include '../database/koneksi.php';

// Cek apakah Expert sudah login
if (!isset($_SESSION['Id_User']) && !isset($_SESSION['id_user'])) {
    header("Location: ../auth/masuk.php");
    exit;
}

$id_expert = $_SESSION['Id_User'] ?? $_SESSION['id_user'];

// Ambil data profil Expert yang sedang login
$query_expert = mysqli_query($connect, "SELECT * FROM user WHERE Id_User = '$id_expert'");
$data_expert  = mysqli_fetch_assoc($query_expert);

$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
$pesan_error  = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_sukses'], $_SESSION['pesan_error']);

// 1. PROSES SIMPAN TANGGAPAN BARU EXPERT (POIN 4 EXPERT)
if (isset($_POST['balas_topik'])) {
    $id_topik   = (int)($_POST['id_topik'] ?? 0);
    $isi_respon = trim($_POST['isi_respon'] ?? '');

    if (empty($isi_respon)) {
        $_SESSION['pesan_error'] = "Tanggapan wajib diisi dan tidak boleh kosong!";
        header("Location: diskusi.php");
        exit;
    } elseif (strlen($isi_respon) < 10) {
        $_SESSION['pesan_error'] = "Tanggapan terlalu singkat, minimal 10 karakter.";
        header("Location: diskusi.php");
        exit;
    }

    $cek_topik = mysqli_query($connect, "SELECT Status FROM topik WHERE Id_Topik = '$id_topik'");
    $data_cek  = mysqli_fetch_assoc($cek_topik);

    if ($data_cek && $data_cek['Status'] !== 'Selesai') {
        $isi_respon_safe = mysqli_real_escape_string($connect, $isi_respon);

        $query_insert = "INSERT INTO respon (Id_Topik, Id_User, Isi_Respon) 
                         VALUES ('$id_topik', '$id_expert', '$isi_respon_safe')";

        if (mysqli_query($connect, $query_insert)) {
            mysqli_query($connect, "UPDATE topik SET Status = 'Selesai' WHERE Id_Topik = '$id_topik'");
            $_SESSION['pesan_sukses'] = "Tanggapan resmi berhasil dikirim dan topik telah ditutup!";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan tanggapan: " . mysqli_error($connect);
        }
    }
    header("Location: diskusi.php");
    exit;
}

// 2. PROSES EDIT TANGGAPAN EXPERT (POIN 5 EXPERT)
if (isset($_POST['update_respon'])) {
    $id_respon  = (int)($_POST['id_respon'] ?? 0);
    $isi_respon = trim($_POST['isi_respon'] ?? '');

    if (empty($isi_respon)) {
        $_SESSION['pesan_error'] = "Isi tanggapan tidak boleh kosong saat diperbarui!";
        header("Location: diskusi.php?edit_respon=" . $id_respon);
        exit;
    } elseif (strlen($isi_respon) < 10) {
        $_SESSION['pesan_error'] = "Tanggapan terlalu singkat, minimal 10 karakter.";
        header("Location: diskusi.php?edit_respon=" . $id_respon);
        exit;
    }

    $isi_respon_safe = mysqli_real_escape_string($connect, $isi_respon);
    $query_update = "UPDATE respon SET Isi_Respon = '$isi_respon_safe' 
                     WHERE Id_Respon = '$id_respon' AND Id_User = '$id_expert'";

    if (mysqli_query($connect, $query_update)) {
        $_SESSION['pesan_sukses'] = "Tanggapan Anda berhasil diperbarui!";
    } else {
        $_SESSION['pesan_error'] = "Gagal memperbarui tanggapan: " . mysqli_error($connect);
    }
    header("Location: diskusi.php");
    exit;
}

// Filter tampilan (Semua Topik vs Topik yang Sudah Saya Respon - Poin 6 & 7 Expert)
$filter      = $_GET['filter'] ?? 'semua';
$edit_respon = isset($_GET['edit_respon']) ? (int)$_GET['edit_respon'] : 0;

if ($filter === 'saya') {
    $query_topik = mysqli_query($connect, "
        SELECT DISTINCT t.*, k.Nama_Kategori, u.Username 
        FROM topik t
        INNER JOIN respon r ON t.Id_Topik = r.Id_Topik
        LEFT JOIN kategori k ON t.Id_Kategori = k.Id_Kategori
        LEFT JOIN user u ON t.Id_User = u.Id_User
        WHERE r.Id_User = '$id_expert'
        ORDER BY t.Created_At DESC
    ");
} else {
    $query_topik = mysqli_query($connect, "
        SELECT t.*, k.Nama_Kategori, u.Username 
        FROM topik t
        LEFT JOIN kategori k ON t.Id_Kategori = k.Id_Kategori
        LEFT JOIN user u ON t.Id_User = u.Id_User
        ORDER BY (t.Status = 'Selesai') ASC, t.Created_At DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Panel Expert</title>
    <link rel="stylesheet" href="../client/css/diskusi.css">
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

            <div class="profile" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 14px; font-weight: 600;">
                    <?= htmlspecialchars($data_expert['Username'] ?? 'Expert'); ?>
                </span>
                <a href="edit_profil.php" class="avatar" title="Profil Expert">
                    <i class="fa-solid fa-user-shield"></i>
                </a>
            </div>
        </header>

        <section class="content">

            <!-- NOTIFIKASI PHP -->
            <?php if (!empty($pesan_sukses)) : ?>
                <div style="background-color: #dcfce7; border-left: 4px solid #16a34a; color: #166534; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px;">
                    <?= htmlspecialchars($pesan_sukses); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($pesan_error)) : ?>
                <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px;">
                    <?= htmlspecialchars($pesan_error); ?>
                </div>
            <?php endif; ?>

            <!-- HEADER & FILTER EXPERT (POIN 6 & 7 EXPERT) -->
            <div class="discussion-heading" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                <h2>Daftar Topik & Aspirasi Masyarakat</h2>
                <div style="display: flex; gap: 8px;">
                    <a href="diskusi.php?filter=semua" class="btn <?= ($filter === 'semua') ? '' : 'btn-outline-cancel'; ?>" style="text-decoration: none;">Semua Diskusi</a>
                    <a href="diskusi.php?filter=saya" class="btn <?= ($filter === 'saya') ? '' : 'btn-outline-cancel'; ?>" style="text-decoration: none;">Riwayat Respon Saya</a>
                </div>
            </div>

            <div class="topic-list">
                <?php if (mysqli_num_rows($query_topik) > 0) : ?>
                    <?php while ($topik = mysqli_fetch_assoc($query_topik)) : ?>
                        <?php
                        $id_topik = $topik['Id_Topik'];

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

                            <!-- TAMPILKAN LAMPIRAN JIKA ADA -->
                            <?php if (!empty($topik['Lampiran'])) : ?>
                                <?php 
                                $file_lampiran = $topik['Lampiran'];
                                $ext_lampiran  = strtolower(pathinfo($file_lampiran, PATHINFO_EXTENSION));
                                $path_lampiran = "../uploads/" . htmlspecialchars($file_lampiran);
                                ?>
                                <div style="margin: 12px 0;">
                                    <?php if (in_array($ext_lampiran, ['jpg', 'jpeg', 'png'])) : ?>
                                        <a href="<?= $path_lampiran; ?>" target="_blank" title="Klik untuk memperbesar gambar">
                                            <img src="<?= $path_lampiran; ?>" 
                                                 alt="Lampiran Diskusi" 
                                                 style="max-width: 220px; max-height: 160px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1; display: block;">
                                        </a>
                                        <small style="color: #64748b; font-size: 11px;">Klik gambar untuk memperbesar</small>
                                    <?php else : ?>
                                        <a href="<?= $path_lampiran; ?>" target="_blank" style="display: inline-block; padding: 6px 12px; background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; color: #2563eb; text-decoration: none;">
                                            <i class="fa-solid fa-file-pdf"></i> Buka Dokumen PDF (<?= htmlspecialchars($file_lampiran); ?>)
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- AREA TANGGAPAN EXPERT -->
                            <div class="response-area">
                                <h4>Tanggapan Expert</h4>

                                <?php if ($sudah_ditanggapi) : ?>
                                    <?php while ($respon = mysqli_fetch_assoc($query_respon)) : ?>
                                        <div class="response-card">
                                            <div class="expert-name" style="display: flex; justify-content: space-between; align-items: center;">
                                                <div>
                                                    <span class="expert-avatar"><i class="fa-solid fa-user-shield"></i></span>
                                                    <strong><?= htmlspecialchars($respon['Nama_Expert'] ?? 'Expert'); ?></strong>
                                                    <span class="expert-label">Expert</span>
                                                </div>

                                                <!-- TOMBOL EDIT RESPON JIKA MILIK EXPERT YANG LOGIN (POIN 5 EXPERT) -->
                                                <?php if ($respon['Id_User'] == $id_expert) : ?>
                                                    <a href="diskusi.php?edit_respon=<?= $respon['Id_Respon']; ?>" style="font-size: 12px; color: #2563eb; text-decoration: underline;">
                                                        <i class="fa-solid fa-pen"></i> Edit Tanggapan
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($edit_respon == $respon['Id_Respon'] && $respon['Id_User'] == $id_expert) : ?>
                                                <!-- FORM EDIT TANGGAPAN EXPERT -->
                                                <form action="diskusi.php" method="POST" style="margin-top: 10px;">
                                                    <input type="hidden" name="id_respon" value="<?= $respon['Id_Respon']; ?>">
                                                    <textarea name="isi_respon" rows="3" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 8px;"><?= htmlspecialchars($respon['Isi_Respon'] ?? ''); ?></textarea>
                                                    <div style="display: flex; gap: 8px;">
                                                        <button type="submit" name="update_respon" class="btn">Simpan Perubahan</button>
                                                        <a href="diskusi.php" class="btn btn-outline-cancel" style="text-decoration: none;">Batal</a>
                                                    </div>
                                                </form>
                                            <?php else : ?>
                                                <p><?= nl2br(htmlspecialchars($respon['Isi_Respon'] ?? $respon['Respon'] ?? '')); ?></p>
                                                <?php if (!empty($respon['Created_At'])) : ?>
                                                    <small><?= date('d M Y H:i', strtotime($respon['Created_At'])); ?></small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                    <p style="font-size: 13px; color: #64748b; margin-top: 8px;">
                                        <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> Topik ini sudah selesai ditanggapi dan ditutup.
                                    </p>
                                <?php else : ?>
                                    <!-- FORM TANGGAPAN BARU (TANPA ATRIBUT REQUIRED) -->
                                    <form action="diskusi.php" method="POST" class="reply-form">
                                        <input type="hidden" name="id_topik" value="<?= $id_topik; ?>">
                                        <textarea name="isi_respon" placeholder="Tuliskan tanggapan resmi untuk menyelesaikan topik ini..."></textarea>
                                        <button type="submit" name="balas_topik" class="btn">Kirim Tanggapan & Tutup Topik</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="topic-card" style="text-align: center; padding: 30px;">
                        <p>Belum ada topik diskusi yang tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>

</div>

</body>
</html>