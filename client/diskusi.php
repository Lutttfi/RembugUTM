<?php
session_start();
include '../database/koneksi.php';

// Ambil pesan error & input lama dari session (jika validasi gagal) lalu hapus
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
    <title>RembugUTM - Form Diskusi</title>
    <link rel="stylesheet" href="css/diskusi.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR WITH REMBUG UTM BRANDING -->
    <aside class="sidebar">
        <a href="index.php" class="logo">
            <span class="logo-icon">R</span> Rembug<b>UTM</b>
        </a>

        <nav class="menu">
            <a href="beranda.php">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </a>
            <a href="diskusi.php" class="active">
                <i class="fa-solid fa-comments"></i> <span>Forum Diskusi</span>
            </a>
            <a href="riwayat.php">
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

                <!-- NOTIFIKASI ERROR VALIDASI -->
                <?php if (!empty($errors)) : ?>
                    <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px;">
                        <strong>Periksa kembali isian Anda:</strong>
                        <ul style="margin: 6px 0 0 18px; padding: 0;">
                            <?php foreach ($errors as $err) : ?>
                                <li><?= htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="validasi.php" method="POST" enctype="multipart/form-data" class="form-body" id="formDiskusi">

                    <!-- JENIS TOPIK -->
                    <div class="form-group-row">
                        <label class="form-label">Jenis Topik <span style="color: #ef4444;">*</span></label>
                        <div class="form-input radio-group">
                            <label class="radio-label">
                                <input type="radio" name="jenis_laporan" value="Diskusi" <?= (!isset($old_data['jenis_laporan']) || $old_data['jenis_laporan'] === 'Diskusi') ? 'checked' : ''; ?> required> Diskusi Umum
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="jenis_laporan" value="Pertanyaan" <?= (isset($old_data['jenis_laporan']) && $old_data['jenis_laporan'] === 'Pertanyaan') ? 'checked' : ''; ?>> Pertanyaan / Tanya Jawab
                            </label>
                        </div>
                    </div>

                    <!-- JUDUL -->
                    <div class="form-group-row">
                        <label class="form-label" for="judul">Judul Topik <span style="color: #ef4444;">*</span></label>
                        <div class="form-input">
                            <input type="text" id="judul" name="judul" placeholder="Tulis topik atau pertanyaan yang ingin dibahas..." value="<?= htmlspecialchars($old_data['judul'] ?? ''); ?>" required minlength="5">
                        </div>
                    </div>

                    <!-- KATEGORI -->
                    <div class="form-group-row">
                        <label class="form-label" for="kategori">Kategori <span style="color: #ef4444;">*</span></label>
                        <div class="form-input">
                            <select id="kategori" name="kategori" required>
                                <option value="">Pilih kategori diskusi...</option>
                                <?php while ($row = mysqli_fetch_assoc($query_kategori)) : ?>
                                    <option value="<?= $row['Id_Kategori']; ?>" <?= (isset($old_data['kategori']) && $old_data['kategori'] == $row['Id_Kategori']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($row['Nama_Kategori']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <!-- ISI DISKUSI -->
                    <div class="form-group-row">
                        <label class="form-label" for="isi">Isi Diskusi <span style="color: #ef4444;">*</span></label>
                        <div class="form-input">
                            <textarea id="isi" name="isi" placeholder="Uraikan topik pembahasan, latar belakang, atau pertanyaan Anda secara jelas..." required minlength="15"><?= htmlspecialchars($old_data['isi'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- LAMPIRAN -->
                    <div class="form-group-row">
                        <label class="form-label">Lampiran <small class="text-muted">(Opsional)</small></label>
                        <div class="form-input">
                            <div class="file-upload-box">
                                <input type="file" id="lampiran" name="lampiran" class="file-input-native" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                            <span class="input-hint">Format: JPG, PNG, PDF (maks. 5MB)</span>
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

<!-- VALIDASI CLIENT-SIDE JAVASCRIPT -->
<script>
document.getElementById('formDiskusi').addEventListener('submit', function(e) {
    const judul = document.getElementById('judul').value.trim();
    const kategori = document.getElementById('kategori').value;
    const isi = document.getElementById('isi').value.trim();

    if (judul === '' || kategori === '' || isi === '') {
        e.preventDefault();
        alert('Judul, Kategori, dan Isi Diskusi wajib diisi dan tidak boleh hanya berisi spasi!');
    }
});
</script>

</body>
</html>