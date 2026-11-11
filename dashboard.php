<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
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
            <a href="dashboard.html" class="active">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </a>
            <a href="diskusi.php">
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

            <!-- ========================================================
                 VERSI 1: DASHBOARD CLIENT (SEPERTI FOTO PERTAMA)
                 ======================================================== -->
            <div class="dashboard-section">
                <div class="dashboard-header">
                    <h1>Dashboard</h1>
                    <p>Selamat datang, Ririn!</p>
                </div>

                <!-- STATS CARDS -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon icon-blue">
                            <i class="fa-solid fa-message"></i>
                        </div>
                        <div class="stat-info">
                            <h2>1</h2>
                            <span>Total Laporan</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon icon-blue-light">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h2>1</h2>
                            <span>Dalam Proses</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon icon-green">
                            <i class="fa-solid fa-square-check"></i>
                        </div>
                        <div class="stat-info">
                            <h2>0</h2>
                            <span>Selesai</span>
                        </div>
                    </div>
                </div>

                <!-- LAPORAN TERBARU CARD -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Laporan Terbaru</h2>
                        <a href="diskusi.php" class="link-more">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <div class="report-list">
                        <div class="report-item">
                            <div class="report-main">
                                <div class="report-icon">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div class="report-details">
                                    <h3>Masalah fasilitas perpustakaan</h3>
                                    <span class="report-date">10 Nov 2026</span>
                                </div>
                            </div>
                            <span class="badge badge-process">Dalam Proses</span>
                        </div>
                    </div>
                </div>

                <!-- TIPS BOX -->
                <div class="tips-box">
                    <div class="tips-icon">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <div class="tips-content">
                        <h4>Tips</h4>
                        <p>Pastikan laporan Anda jelas dan sesuai dengan kategori yang tersedia.</p>
                    </div>
                </div>
            </div>

            <br><hr style="border: 1px dashed #c5d3ea; margin: 40px 0;"><br>

            <!-- ========================================================
                 VERSI 2: DASHBOARD EXPERT (SEPERTI FOTO KEDUA)
                 ======================================================== -->
            <div class="dashboard-section">
                <div class="dashboard-header">
                    <h1>Dashboard - Expert</h1>
                    <p>Kelola laporan dan tanggapi aspirasi mahasiswa.</p>
                </div>

                <!-- STATS CARDS -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon icon-blue">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div class="stat-info">
                            <h2>5</h2>
                            <span>Total Laporan</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon icon-blue-light">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h2>3</h2>
                            <span>Dalam Proses</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon icon-green">
                            <i class="fa-solid fa-square-check"></i>
                        </div>
                        <div class="stat-info">
                            <h2>2</h2>
                            <span>Selesai</span>
                        </div>
                    </div>
                </div>

                <!-- LAPORAN TERBARU EXPERT CARD -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Laporan Terbaru</h2>
                        <a href="diskusi.php" class="link-more">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <div class="report-list">
                        <!-- ITEM 1 -->
                        <div class="report-item">
                            <div class="report-main">
                                <div class="report-icon">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div class="report-details">
                                    <h3>Permintaan penambahan ruang belajar</h3>
                                    <span class="report-date">10 Nov 2026</span>
                                </div>
                            </div>
                            <span class="badge badge-process">Dalam Proses</span>
                        </div>

                        <!-- ITEM 2 -->
                        <div class="report-item">
                            <div class="report-main">
                                <div class="report-icon">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div class="report-details">
                                    <h3>Keluhan fasilitas AC</h3>
                                    <span class="report-date">09 Nov 2026</span>
                                </div>
                            </div>
                            <span class="badge badge-new">Baru</span>
                        </div>

                        <!-- ITEM 3 -->
                        <div class="report-item">
                            <div class="report-main">
                                <div class="report-icon">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div class="report-details">
                                    <h3>Saran peningkatan WiFi</h3>
                                    <span class="report-date">08 Nov 2026</span>
                                </div>
                            </div>
                            <span class="badge badge-done">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>

        </section>

    </main>

</div>

</body>
</html>