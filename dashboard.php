<?php
    $nama_user = "Bila";
    $total_laporan = 1;
    $dalam_proses = 1;
    $selesai = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Aspirasi Kampus</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <div class="wrapper">
        <!-- Sidebar langsung dimasukkan ke sini (Tanpa file terpisah) -->
        <div class="sidebar">
            <div>
                <div class="logo">
                    <span>💬</span> Aspirasi Kampus
                </div>
                <ul class="menu-list">
                    <li><a href="dashboard.php" class="aktif">🏠 Dashboard</a></li>
                    <li><a href="#">📋 Laporan</a></li>
                    <li><a href="#">🕒 Riwayat Laporan</a></li>
                </ul>
            </div>
            <!-- <div>
                <a href="#" class="btn-keluar">🚪 Keluar</a>
            </div> -->
        </div>

        <!-- Konten Kanan -->
        <div class="content">
            <div class="header-user">
                <span class="user-icon">👤</span>
            </div>

            <div class="sapaan">
                <h2>Halo, <?php echo $nama_user; ?>!</h2>
                <p>Selamat datang di Aspirasi Kampus. Sampaikan aspirasi Anda untuk kampus yang lebih baik.</p>
            </div>

            <div class="cards-flex">
                <div class="card">
                    <div class="card-icon icon-blue">💬</div>
                    <div class="card-info">
                        <h3><?php echo $total_laporan; ?></h3>
                        <span>Total Laporan</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon icon-cyan">🕒</div>
                    <div class="card-info">
                        <h3><?php echo $dalam_proses; ?></h3>
                        <span>Dalam Proses</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon icon-green">✔</div>
                    <div class="card-info">
                        <h3><?php echo $selesai; ?></h3>
                        <span>Selesai</span>
                    </div>
                </div>
            </div>

            <div class="box-laporan">
                <div class="box-header">
                    <h3>📢 Laporan Terbaru</h3>
                    <a href="#">Lihat Semua &rarr;</a>
                </div>
                <div class="item-laporan">
                    <div class="item-left">
                        <div class="icon-file">📄</div>
                        <div class="item-text">
                            <h4>Fasilitas WiFi di Perpustakaan</h4>
                            <p>Dikirim pada 10 Nov 2025 • Dalam Proses</p>
                        </div>
                    </div>
                    <span class="status-tag">Dalam Proses</span>
                </div>
            </div>

            <div class="banner-tips">
                <div class="tips-text">
                    <h4>💡 Tips Pengajuan Laporan</h4>
                    <p>Pastikan aspirasi Anda jelas dan sesuai dengan topik yang relevan dengan kampus.</p>
                </div>
                <div class="tips-icon">📢</div>
            </div>

        </div>
    </div>

</body>
</html>