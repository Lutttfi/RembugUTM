<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR WITH REMBUG UTM BRANDING -->
    <aside class="sidebar">
        <a href="index.php" class="logo">
            <span class="logo-icon">R</span> Rembug<b>UTM</b>
        </a>

        <nav class="menu">
            <a href="dashboard.php" class="active">
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

        <a href="auth/logout.php" class="logout">
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

                 <!-- beranda EXPERT  -->
                  
            <div class="beranda-section">
                <div class="beranda-header">
                    <h1>Beranda - Expert</h1>
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
                <div class="beranda-card">
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