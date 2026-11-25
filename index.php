<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RembugRakyat</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <a href="#beranda" class="logo"><span class="logo-icon">R</span> Rembug<b>Rakyat</b></a>

    <nav class="menu">
      <a href="#beranda">Beranda</a>
      <a href="#tentang">Tentang</a>
    </nav>

    <div class="auth">
      <a href="auth/masuk.php" class="btn btn-outline">Masuk</a>
      <a href="auth/daftar.php" class="btn">Daftar</a>
    </div>
  </header>

  <!-- Beranda -->
  <section id="beranda" class="hero">
    <h1>Welcome!</h1>
    <p>Sampaikan aspirasi, ide, dan masukan untuk kampus yang lebih baik.</p>
  </section>

  <!-- Tentang -->
  <section id="tentang" class="about">

    <div class="about-box light">
      <h2>Tentang RembugRakyat</h2>
      <p>RembugRakyat adalah ruang terbuka bagi Masyarakat untuk menyampaikan laporan, ide, dan masukan dengan mudah, aman, dan transparan.</p>

      <ul class="features">
        <li><span class="icon">🔒</span><div><b>Aman &amp; Privat</b><br>Identitas pelapor dijaga kerahasiaannya.</div></li>
        <li><span class="icon">⚡</span><div><b>Cepat Ditanggapi</b><br>Laporan langsung diteruskan ke pihak terkait.</div></li>
        <li><span class="icon">👁️</span><div><b>Transparan</b><br>Status laporan bisa dipantau kapan saja.</div></li>
      </ul>
    </div>

    <div class="about-box dark">
      <h2>Cara Membuat Laporan</h2>
      <p>Ikuti 4 langkah mudah berikut untuk menyampaikan laporan.</p>

      <ol class="steps">
        <li><span class="num">1</span><div><b>Buat Akun / Masuk</b><br>Daftar dengan email kampus kamu.</div></li>
        <li><span class="num">2</span><div><b>Tulis Laporan</b><br>Isi judul, kategori, dan detail masalah.</div></li>
        <li><span class="num">3</span><div><b>Kirim &amp; Pantau</b><br>Kirim laporan lalu lihat perkembangannya.</div></li>
        <li><span class="num">4</span><div><b>Terima Tanggapan</b><br>Pihak kampus akan membalas laporanmu.</div></li>
      </ol>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-wrap">
      <div class="footer-brand">
        <a href="#beranda" class="logo"><span class="logo-icon">R</span> Rembug<b>Rakyat</b></a>
        <p>Platform aspirasi dan laporan mahasiswa untuk kampus yang lebih baik.</p>
      </div>

      <div class="footer-col">
        <h4>Navigasi</h4>
        <a href="#beranda">Beranda</a>
        <a href="#trending">Trending</a>
        <a href="#tentang">Tentang</a>
        <a href="#kontak">Kontak</a>
      </div>

      <div class="footer-col">
        <h4>Layanan</h4>
        <a href="#">Buat Laporan</a>
        <a href="#">Pantau Laporan</a>
        <a href="#">Pertanyaan Umum</a>
      </div>
    </div>

    <p class="copyright">© 2026 RembugUTM. Semua hak dilindungi.</p>
  </footer>

</body>
</html>