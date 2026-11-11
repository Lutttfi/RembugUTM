<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RembugUTM</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <a href="#beranda" class="logo"><span class="logo-icon">R</span> Rembug<b>UTM</b></a>

    <nav class="menu">
      <a href="#beranda">Beranda</a>
      <a href="#trending">Trending</a>
      <a href="#tentang">Tentang</a>
      <a href="#kontak">Kontak</a>
    </nav>

    <div class="auth">
      <a href="masuk.php" class="btn btn-outline">Masuk</a>
      <a href="daftar.php" class="btn">Daftar</a>
    </div>
  </header>

  <!-- Beranda -->
  <section id="beranda" class="hero">
    <h1>Welcome!</h1>
    <p>Sampaikan aspirasi, ide, dan masukan untuk kampus yang lebih baik.</p>
  </section>

  <!-- Trending Topik -->
  <section id="trending" class="trending">
    <div class="section-head">
      <h2>🔥 Trending Topik</h2>
      <a href="#" class="btn btn-outline-light">Lihat Semua Topik</a>
    </div>

    <!-- Filter topik -->
    <div class="chips">
      <span class="chip active">Semua Topik</span>
      <span class="chip">Fasilitas Kampus</span>
      <span class="chip">UKT</span>
      <span class="chip">Akademik</span>
      <span class="chip">Kemahasiswaan</span>
      <span class="chip">Layanan Kampus</span>
    </div>

    <div class="cards">
      <article class="card">
        <span class="tag">Fasilitas</span>
        <h3>Perbaikan AC di Gedung Kuliah Utama Lantai 3</h3>
        <p>Beberapa ruang kelas di lantai 3 AC-nya tidak dingin sehingga kegiatan belajar terganggu.</p>
        <div class="stats"><span>👍 124 Dukungan</span><span>💬 32 Komentar</span></div>
      </article>

      <article class="card">
        <span class="tag">UKT</span>
        <h3>Penyesuaian Pembayaran dan Keringanan UKT Semester Ini</h3>
        <p>Mahasiswa meminta kejelasan jadwal dan syarat pengajuan keringanan UKT.</p>
        <div class="stats"><span>👍 98 Dukungan</span><span>💬 21 Komentar</span></div>
      </article>

      <article class="card">
        <span class="tag">Akademik</span>
        <h3>Pemeliharaan Sistem Nilai Tepat Waktu dan Penyampaian Jadwal Ujian</h3>
        <p>Nilai dan jadwal ujian diharapkan diumumkan lebih cepat agar mahasiswa bisa bersiap.</p>
        <div class="stats"><span>👍 76 Dukungan</span><span>💬 18 Komentar</span></div>
      </article>
    </div>
  </section>

  <!-- Tentang -->
  <section id="tentang" class="about">

    <div class="about-box light">
      <h2>Tentang RembugUTM</h2>
      <p>RembugUTM adalah ruang terbuka bagi mahasiswa untuk menyampaikan laporan, ide, dan masukan kepada pihak kampus dengan mudah, aman, dan transparan.</p>

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

  <!-- kontak -->
  <section id="kontak" class="contact">
    <h2>Kontak &amp; Layanan Bantuan</h2>
    <p class="sub">Butuh bantuan? Hubungi kami lewat kontak berikut atau kirim pesan langsung lewat formulir.</p>

    <div class="contact-wrap">
      <div class="contact-info">
        <div class="info-item"><span class="icon">✉️</span><div><b>Email Resmi</b><br>layanan@rembugutm.ac.id</div></div>
        <div class="info-item"><span class="icon">💬</span><div><b>Helpdesk WhatsApp</b><br>+62 812-3456-7890</div></div>
        <div class="info-item"><span class="icon">📍</span><div><b>Lokasi Layanan Kampus</b><br>Gedung Layanan Mahasiswa, Kampus UTM</div></div>
      </div>

      <form class="contact-form">
        <h3>Kirim Pesan &amp; Masukan Cepat</h3>
        <label for="nama">Nama</label>
        <input type="text" id="nama" placeholder="Nama lengkap">

        <label for="email">Email</label>
        <input type="email" id="email" placeholder="nama@email.com">

        <label for="pesan">Pesan</label>
        <textarea id="pesan" rows="4" placeholder="Tulis pesan kamu di sini..."></textarea>

        <button type="submit" class="btn">Kirim Pesan</button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-wrap">
      <div class="footer-brand">
        <a href="#beranda" class="logo"><span class="logo-icon">R</span> Rembug<b>UTM</b></a>
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