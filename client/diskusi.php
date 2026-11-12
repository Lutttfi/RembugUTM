<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Form Laporan</title>
    <link rel="stylesheet" href="client/css/diskusi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR WITH REMBUG UTM BRANDING -->
    <aside class="sidebar">
        <a href="index.php" class="logo">
            <span class="logo-icon">R</span> Rembug<b>UTM</b>
        </a>

        <nav class="menu">
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </a>
            <a href="diskusi.php" class="active">
                <i class="fa-solid fa-file-lines"></i> <span>Laporan</span>
            </a>
            <a href="riwayat.php">
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Laporan</span>
            </a>
            <a href="profil.php">
                <i class="fa-solid fa-user-gear"></i> <span>Profil Saya</span>
            </a>
        </nav>

        <a href="logout.php" class="logout">
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
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div class="header-text">
                        <h1>Form Laporan / Diskusi</h1>
                        <p>Sampaikan aspirasi Anda dengan jelas dan sopan.</p>
                    </div>
                </div>

                <form action="diskusi.php" method="POST" enctype="multipart/form-data" class="form-body">

                    <!-- JENIS LAPORAN -->
                    <div class="form-group-row">
                        <label class="form-label">Jenis Laporan</label>
                        <div class="form-input radio-group">
                            <label class="radio-label">
                                <input type="radio" name="jenis_laporan" value="Laporan" checked> Laporan
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="jenis_laporan" value="Diskusi"> Diskusi
                            </label>
                        </div>
                    </div>

                    <!-- JUDUL -->
                    <div class="form-group-row">
                        <label class="form-label" for="judul">Judul</label>
                        <div class="form-input">
                            <input type="text" id="judul" name="judul" placeholder="Masukkan judul laporan...">
                        </div>
                    </div>

                    <!-- KATEGORI -->
                    <div class="form-group-row">
                        <label class="form-label" for="kategori">Kategori</label>
                        <div class="form-input">
                            <select id="kategori" name="kategori">
                                <option value="">Pilih kategori...</option>
                                <option value="1">Fasilitas Kampus</option>
                                <option value="2">Akademik & Kurikulum</option>
                                <option value="3">Layanan Keuangan</option>
                                <option value="4">Kemahasiswaan</option>
                            </select>
                        </div>
                    </div>

                    <!-- ISI LAPORAN -->
                    <div class="form-group-row">
                        <label class="form-label" for="isi">Isi Laporan</label>
                        <div class="form-input">
                            <textarea id="isi" name="isi" placeholder="Tulis detail laporan atau diskusi Anda di sini..."></textarea>
                        </div>
                    </div>

                    <!-- LAMPIRAN -->
                    <div class="form-group-row">
                        <label class="form-label">Lampiran <small class="text-muted">(Opsional)</small></label>
                        <div class="form-input">
                            <div class="file-upload-box">
                                <input type="file" id="lampiran" name="lampiran" class="file-input-native">
                            </div>
                            <span class="input-hint">Format: JPG, PNG, PDF (maks. 5MB)</span>
                        </div>
                    </div>

                    <!-- FOOTER BUTTONS -->
                    <div class="form-footer-actions">
                        <button type="reset" class="btn btn-outline-cancel">Batal</button>
                        <button type="submit" name="buat_topik" class="btn">Kirim Laporan</button>
                    </div>

                </form>
            </div>

        </section>

    </main>

</div>

</body>
</html>