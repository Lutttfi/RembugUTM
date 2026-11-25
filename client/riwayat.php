<?php
session_start();
include '../database/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['Id_User']) && !isset($_SESSION['id_user'])) {
    header("Location: ../auth/masuk.php");
    exit;
}

$id_user = $_SESSION['Id_User'] ?? $_SESSION['id_user'];

// Ambil data profil user yang sedang login
$query_user = mysqli_query($connect, "SELECT * FROM user WHERE Id_User = '$id_user'");
$data_user  = mysqli_fetch_assoc($query_user);

if (!$data_user) {
    session_destroy();
    header("Location: ../auth/masuk.php");
    exit;
}

// Proteksi Role: Hanya 'client' yang boleh mengakses halaman ini
$role = strtolower($data_user['Role'] ?? $data_user['role'] ?? '');
if ($role !== 'client') {
    header("Location: ../expert/beranda.php");
    exit;
}

// Ambil pesan sukses/error dari session
$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
$errors       = $_SESSION['errors'] ?? [];
unset($_SESSION['pesan_sukses'], $_SESSION['errors']);

// Cek apakah user sedang menekan tombol edit pada salah satu topik (?edit=ID)
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

// Ambil data kategori untuk dropdown saat edit topik
$daftar_kategori = [];
$q_kat = mysqli_query($connect, "SELECT * FROM kategori ORDER BY Id_Kategori ASC");
while ($k = mysqli_fetch_assoc($q_kat)) {
    $daftar_kategori[] = $k;
}

// Ambil riwayat topik/laporan KHUSUS milik user yang sedang login
$query_topik = mysqli_query($connect, "
    SELECT t.*, k.Nama_Kategori, u.Username 
    FROM topik t
    LEFT JOIN kategori k ON t.Id_Kategori = k.Id_Kategori
    LEFT JOIN user u ON t.Id_User = u.Id_User
    WHERE t.Id_User = '$id_user'
    ORDER BY t.Created_At DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rembug Rakyat - Riwayat Diskusi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="beranda.php" class="logo">
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
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Laporan</span>
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
                <a href="beranda.php" class="logo logo-dark">
                    <span class="ikon-logo">R</span> Rembug<b>Rakyat</b>
                </a>
            </div>

            <div class="profile" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 14px; font-weight: 600;">
                    <?= htmlspecialchars($data_user['Username'] ?? $data_user['username'] ?? 'Pengguna'); ?>
                </span>
                <a href="profil.php" class="avatar" title="Lihat Profil">
                    <i class="fa-solid fa-user"></i>
                </a>
            </div>
        </header>

        <section class="content">

            <!-- NOTIFIKASI SUKSES / ERROR -->
            <?php if (!empty($pesan_sukses)) : ?>
                <div style="background-color: #dcfce7; border-left: 4px solid #16a34a; color: #166534; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px;">
                    <?= htmlspecialchars($pesan_sukses); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)) : ?>
                <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px;">
                    <strong>Gagal memperbarui laporan/topik:</strong>
                    <ul style="margin: 6px 0 0 18px; padding: 0;">
                        <?php foreach ($errors as $err) : ?>
                            <li><?= htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- HEADER & TOMBOL BUAT TOPIK BARU -->
            <div class="discussion-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Riwayat Laporan & Diskusi Saya</h2>
                <a href="diskusi.php" class="btn" style="text-decoration: none;">+ Buat Topik Baru</a>
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
                        $sudah_ditanggapi = ($jumlah_respon > 0 || ($topik['Status'] ?? '') === 'Selesai');
                        ?>

                        <!-- TOPIC CARD DINAMIS -->
                        <div class="topic-card">
                            <div class="topic-top">
                                <span class="topic-category"><?= htmlspecialchars($topik['Nama_Kategori'] ?? 'Umum'); ?></span>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if ($sudah_ditanggapi) : ?>
                                        <span class="topic-status sudah-ditanggapi">Sudah Ditanggapi</span>
                                    <?php else : ?>
                                        <span class="topic-status menunggu-tanggapan">Menunggu Tanggapan</span>
                                        <a href="riwayat.php?edit=<?= $id_topik; ?>" style="font-size: 13px; color: #2563eb; text-decoration: underline;">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit Laporan
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($edit_id == $id_topik && !$sudah_ditanggapi) : ?>
                                <!-- FORM EDIT LAPORAN CLIENT -->
                                <form action="validasi.php" method="POST" enctype="multipart/form-data" style="margin-top: 15px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #cbd5e1;">
                                    <input type="hidden" name="id_topik" value="<?= $id_topik; ?>">
                                    
                                    <div style="margin-bottom: 10px;">
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Judul Laporan / Topik</label>
                                        <input type="text" name="judul" value="<?= htmlspecialchars($topik['Judul_Topik']); ?>" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                    </div>

                                    <div style="margin-bottom: 10px;">
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Kategori</label>
                                        <select name="kategori" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                            <?php foreach ($daftar_kategori as $kat) : ?>
                                                <option value="<?= $kat['Id_Kategori']; ?>" <?= ($topik['Id_Kategori'] == $kat['Id_Kategori']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($kat['Nama_Kategori']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div style="margin-bottom: 10px;">
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Isi Laporan / Diskusi</label>
                                        <textarea name="isi" rows="4" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;"><?= htmlspecialchars($topik['Isi_Diskusi']); ?></textarea>
                                    </div>

                                    <div style="margin-bottom: 10px;">
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Ganti Lampiran <small style="font-weight: normal; color: #64748b;">(Opsional)</small></label>
                                        <input type="file" name="lampiran">
                                    </div>

                                    <div style="display: flex; gap: 10px;">
                                        <button type="submit" name="update_topik" class="btn">Simpan Perubahan</button>
                                        <a href="riwayat.php" class="btn btn-outline-cancel" style="text-decoration: none;">Batal</a>
                                    </div>
                                </form>
                            <?php else : ?>
                                <!-- TAMPILAN NORMAL TOPIK -->
                                <h3><?= htmlspecialchars($topik['Judul_Topik']); ?></h3>

                                <div class="topic-info">
                                    Oleh <strong><?= htmlspecialchars($topik['Username'] ?? 'Saya'); ?></strong> · <?= date('d M Y H:i', strtotime($topik['Created_At'])); ?>
                                </div>

                                <p class="topic-content"><?= nl2br(htmlspecialchars($topik['Isi_Diskusi'])); ?></p>
                            <?php endif; ?>

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
                        <p>Anda belum pernah membuat topik diskusi.</p>
                        <a href="diskusi.php" class="btn" style="display: inline-block; margin-top: 10px; text-decoration: none;">Mulai Topik Pertama</a>
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>

</div>

</body>
</html>