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

$nama_tampil = $data_user['Username'] ?? $data_user['username'] ?? 'Pengguna';

// Ambil pesan error & input lama dari session (validasi server-side PHP)
$errors   = $_SESSION['errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_data']);

// Ambil data kategori dari database untuk dropdown
$query_kategori = mysqli_query($connect, "SELECT * FROM kategori ORDER BY Id_Kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugRakyat - Form Laporan</title>
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
            <a href="diskusi.php" class="active">
                <i class="fa-solid fa-comments"></i> <span>Laporan</span>
            </a>
            <a href="riwayat.php">
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
                    <?= htmlspecialchars($nama_tampil); ?>
                </span>
                <a href="profil.php" class="avatar" title="Lihat Profil">
                    <?= strtoupper(substr(htmlspecialchars($nama_tampil), 0, 1)); ?>
                </a>
            </div>
        </header>

        <section class="content">

            <!-- FORM CLIENT -->
            <div class="form-container">
                <div class="form-header">
                    <div class="header-icon">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <div class="header-text">
                        <h1>Mulai Topik Diskusi</h1>
                        <p>Bagikan gagasan, pertanyaan, atau bahas isu masyarakat secara terbuka dan santun.</p>
                    </div>
                </div>

                <!-- NOTIFIKASI KHUSUS ERROR DATABASE (JIKA ADA) -->
                <?php if (!empty($errors['database'])) : ?>
                    <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px;">
                        <?= htmlspecialchars($errors['database']); ?>
                    </div>
                <?php endif; ?>

                <form action="validasi.php" method="POST" class="form-body">

                    <!-- JUDUL -->
                    <div class="form-group-row">
                        <label class="form-label" for="judul">Judul Topik <span style="color: #ef4444;">*</span></label>
                        <div class="form-input">
                            <input type="text" id="judul" name="judul" placeholder="Tulis topik atau pertanyaan yang ingin dibahas..." value="<?= htmlspecialchars($old_data['judul'] ?? ''); ?>" style="<?= !empty($errors['judul']) ? 'border-color: #ef4444;' : ''; ?>">
                            <?php if (!empty($errors['judul'])) : ?>
                                <small style="color: #ef4444; display: block; margin-top: 5px; font-size: 13px;">
                                    <?= htmlspecialchars($errors['judul']); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- KATEGORI -->
                    <div class="form-group-row">
                        <label class="form-label" for="kategori">Kategori <span style="color: #ef4444;">*</span></label>
                        <div class="form-input">
                            <select id="kategori" name="kategori" style="<?= !empty($errors['kategori']) ? 'border-color: #ef4444;' : ''; ?>">
                                <option value="">Pilih kategori diskusi...</option>
                                <?php while ($row = mysqli_fetch_assoc($query_kategori)) : ?>
                                    <option value="<?= $row['Id_Kategori']; ?>" <?= (isset($old_data['kategori']) && $old_data['kategori'] == $row['Id_Kategori']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($row['Nama_Kategori']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <?php if (!empty($errors['kategori'])) : ?>
                                <small style="color: #ef4444; display: block; margin-top: 5px; font-size: 13px;">
                                    <?= htmlspecialchars($errors['kategori']); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ISI DISKUSI -->
                    <div class="form-group-row">
                        <label class="form-label" for="isi">Isi Diskusi <span style="color: #ef4444;">*</span></label>
                        <div class="form-input">
                            <textarea id="isi" name="isi" placeholder="Uraikan topik pembahasan, latar belakang, atau pertanyaan Anda secara jelas..." style="<?= !empty($errors['isi']) ? 'border-color: #ef4444;' : ''; ?>"><?= htmlspecialchars($old_data['isi'] ?? ''); ?></textarea>
                            <?php if (!empty($errors['isi'])) : ?>
                                <small style="color: #ef4444; display: block; margin-top: 5px; font-size: 13px;">
                                    <?= htmlspecialchars($errors['isi']); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- FOOTER BUTTONS -->
                    <div class="form-footer-actions">
                        <button type="reset" class="btn btn-outline-cancel">Batal</button>
                        <button type="submit" name="buat_topik" class="btn">Terbitkan Diskusi</button>
                    </div>

                </form>
            </div>

        </section>

    </main>

</div>

</body>
</html>