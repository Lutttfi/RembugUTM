<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Form Laporan</title>
    <link rel="stylesheet" href="diskusi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR WITH REMBUG UTM BRANDING -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">R</div>
            <h2>Rembug<span>UTM</span></h2>
        </div>

        <nav class="menu">
            <a href="#">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </a>
            <a href="diskusi.html" class="active">
                <i class="fa-solid fa-file-lines"></i> <span>Laporan</span>
            </a>
            <a href="#">
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Laporan</span>
            </a>
        </nav>

        <a href="#" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i> <span>Keluar</span>
        </a>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR WITH REMBUG UTM BRANDING -->
        <header class="topbar">
            <div class="topbar-title">
                <div class="brand-icon-small">R</div>
                <h2>Rembug<span>UTM</span></h2>
            </div>

            <div class="profile">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
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
                        <button type="reset" class="btn-cancel">Batal</button>
                        <button type="submit" class="btn-submit">Kirim Laporan</button>
                    </div>

                </form>
            </div>

            <!-- LIST TOPIK & DISKUSI -->
            <div class="discussion-heading">
                <h2>Daftar Laporan & Diskusi</h2>
            </div>

            <div class="topic-list">
                
                <!-- TOPIC CARD 1 -->
                <div class="topic-card">
                    <div class="topic-top">
                        <span class="topic-category">Fasilitas Kampus</span>
                        <span class="topic-status sudah-ditanggapi">Sudah Ditanggapi</span>
                    </div>

                    <h3>Proyektor Rusak di Ruang Gedung B3</h3>

                    <div class="topic-info">
                        Oleh <strong>Ahmad Fauzi</strong> · 10 Nov 2026 14:30
                    </div>

                    <p class="topic-content">Proyektor di ruang B3 mati total saat perkuliahan berlangsung. Mohon untuk segera dicek oleh tim sarpras.</p>

                    <div class="response-area">
                        <h4>Tanggapan Expert</h4>
                        
                        <div class="response-card">
                            <div class="expert-name">
                                <span class="expert-avatar"><i class="fa-solid fa-user-shield"></i></span>
                                <strong>Tim Sarpras Kampus</strong>
                                <span class="expert-label">Expert</span>
                            </div>
                            <p>Laporan telah diterima. Tim teknisi kami sedang menuju ke lokasi untuk perbaikan proyektor.</p>
                            <small>10 Nov 2026 15:00</small>
                        </div>

                        <!-- Form Balas (Expert) -->
                        <form action="diskusi.php" method="POST" class="reply-form">
                            <input type="hidden" name="id_topik" value="1">
                            <textarea name="isi_respon" placeholder="Tuliskan tanggapan resmi..."></textarea>
                            <button type="submit" class="btn-submit">Kirim Tanggapan</button>
                        </form>
                    </div>
                </div>

                <!-- TOPIC CARD 2 -->
                <div class="topic-card">
                    <div class="topic-top">
                        <span class="topic-category">Akademik & Kurikulum</span>
                        <span class="topic-status menunggu-tanggapan">Menunggu Tanggapan</span>
                    </div>

                    <h3>Jadwal Pengisian KRS Bentrok</h3>

                    <div class="topic-info">
                        Oleh <strong>Siti Rahma</strong> · 11 Nov 2026 09:15
                    </div>

                    <p class="topic-content">Jadwal mata kuliah Pemrograman Web bentrok dengan Basis Data di kelas TI-B. Apakah ada opsi kelas paralel lain?</p>

                    <div class="response-area">
                        <h4>Tanggapan Expert</h4>
                        <p class="empty-response">Belum ada tanggapan dari Expert.</p>

                        <form action="diskusi.php" method="POST" class="reply-form">
                            <input type="hidden" name="id_topik" value="2">
                            <textarea name="isi_respon" placeholder="Tuliskan tanggapan resmi..."></textarea>
                            <button type="submit" class="btn-submit">Kirim Tanggapan</button>
                        </form>
                    </div>
                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>