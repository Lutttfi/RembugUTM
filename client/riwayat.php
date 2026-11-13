<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Riwayat Laporan</title>
    <link rel="stylesheet" href="css/diskusi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <i class="fa-solid fa-file-lines"></i> <span>Laporan</span>
            </a>
            <a href="riwayat.php" class="active">
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

            <!-- LIST TOPIK & DISKUSI (RIWAYAT LAPORAN) -->
            <div class="discussion-heading">
                <h2>Riwayat Laporan & Diskusi</h2>
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
                            <button type="submit" name="balas_topik" class="btn">Kirim Tanggapan</button>
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
                            <button type="submit" name="balas_topik" class="btn">Kirim Tanggapan</button>
                        </form>
                    </div>
                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>