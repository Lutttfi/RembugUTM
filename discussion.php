<?php
// =============================================================================
// FILE        : discussion.php
// DESKRIPSI   : Halaman Form Laporan / Diskusi (Client / Mahasiswa)
// SPESIFIKASI : TA PAW 2026 - Tanpa Framework Front-End/Back-End
// =============================================================================

// 1. Inisialisasi Session Server-Side
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------------------
// AUTO-LOGIN TEST (Hapus / beri komentar pada blok ini jika index.php sudah ada)
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['nama']    = 'Ririn';
    $_SESSION['role']    = 'Client (Mahasiswa)';
}

// -----------------------------------------------------------------------------
// CEK AUTENTIKASI LOGIN (Proteksi Halaman)
// Jika user belum login, lempar kembali ke halaman login (index.php)[cite: 3]
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Mengambil data user aktif dari session login[cite: 3]
$nama_user = $_SESSION['nama'];
$role_user = $_SESSION['role'];

// 2. Inisialisasi Variabel Form & Array Error Validasi[cite: 3]
$jenis_laporan = $_POST['jenis_laporan'] ?? 'Laporan';
$judul         = $_POST['judul'] ?? '';
$kategori      = $_POST['kategori'] ?? '';
$isi           = $_POST['isi'] ?? '';
$errors        = [];

// 3. Proses Validasi Server-Side PHP (TIDAK Menggunakan Validasi HTML Bawaan)[cite: 3]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validasi 1: Judul tidak boleh kosong[cite: 3]
    if (trim($judul) === '') {
        $errors['judul'] = 'Judul laporan wajib diisi.';
    } 
    // Validasi 2: Judul minimal 5 karakter[cite: 3]
    elseif (strlen(trim($judul)) < 5) {
        $errors['judul'] = 'Judul laporan minimal berisi 5 karakter.';
    }

    // Validasi 3: Kategori wajib dipilih[cite: 3]
    if (empty($kategori)) {
        $errors['kategori'] = 'Silakan pilih salah satu kategori yang tersedia.';
    }

    // Validasi 4: Isi Laporan tidak boleh kosong[cite: 3]
    if (trim($isi) === '') {
        $errors['isi'] = 'Isi detail laporan wajib diisi.';
    } 
    // Validasi 5: Isi Laporan minimal 15 karakter[cite: 3]
    elseif (strlen(trim($isi)) < 15) {
        $errors['isi'] = 'Isi laporan terlalu singkat, minimal 15 karakter.';
    }

    // Validasi 6: Format & Ukuran File Lampiran (Opsional, tapi jika diunggah harus valid)[cite: 3]
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
            $fileName   = $_FILES['lampiran']['name'];
            $fileSize   = $_FILES['lampiran']['size'];
            $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext        = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt)) {
                $errors['lampiran'] = 'Format file harus berupa JPG, PNG, atau PDF.';
            } elseif ($fileSize > 5242880) { // Maksimal 5MB
                $errors['lampiran'] = 'Ukuran file tidak boleh melebihi 5 MB.';
            }
        } else {
            $errors['lampiran'] = 'Gagal mengunggah file lampiran.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Laporan / Diskusi - Aspirasi Kampus</title>
    <!-- CSS Eksternal Murni Sesuai Spesifikasi Tugas[cite: 2] -->
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="main-wrapper">

        <!-- Header Navigasi Atas (Top Nav) -->
        <header class="header-nav">
            <div class="brand-logo">
                <div class="brand-icon">
                    <i class="fa-regular fa-comments"></i>
                </div>
                <span>Aspirasi Kampus</span>
            </div>

            <!-- Profile User dari Session Login aktif -->
            <div class="user-profile-header">
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($nama_user); ?></span>
                    <span class="user-role"><?php echo htmlspecialchars($role_user); ?></span>
                </div>
                <div class="user-avatar-btn" title="Akun Pengguna Login">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </header>

        <!-- Main Content Layout -->
        <main class="container">

            <!-- Banner Header Form -->
            <div class="banner-card">
                <div class="banner-icon">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div class="banner-text">
                    <h1>Form Laporan / Diskusi</h1>
                    <p>Sampaikan aspirasi Anda dengan jelas dan sopan.</p>
                </div>
            </div>

            <!-- Card Form Laporan -->
            <div class="form-card">
                
                <!-- CATATAN: Bebas dari atribut validasi HTML bawaan (seperti required, pattern, dsb)[cite: 3] -->
                <form action="discussion.php" method="POST" enctype="multipart/form-data">
                    
                    <!-- 1. Jenis Laporan (Radio Button) -->
                    <div class="form-group">
                        <label class="form-label">Jenis Laporan</label>
                        <div class="form-control-wrap">
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="jenis_laporan" value="Laporan" <?php echo ($jenis_laporan === 'Laporan') ? 'checked' : ''; ?>>
                                    <span>Laporan</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="jenis_laporan" value="Diskusi" <?php echo ($jenis_laporan === 'Diskusi') ? 'checked' : ''; ?>>
                                    <span>Diskusi</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Judul -->
                    <div class="form-group">
                        <label for="judul" class="form-label">Judul</label>
                        <div class="form-control-wrap">
                            <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($judul); ?>" 
                                   placeholder="Masukkan judul laporan..." 
                                   class="form-input <?php echo isset($errors['judul']) ? 'input-error' : ''; ?>">
                            
                            <!-- Pesan Error Validasi Server-Side PHP[cite: 4] -->
                            <?php if (isset($errors['judul'])): ?>
                                <span class="error-text"><?php echo htmlspecialchars($errors['judul']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 3. Kategori -->
                    <div class="form-group">
                        <label for="kategori" class="form-label">Kategori</label>
                        <div class="form-control-wrap">
                            <select id="kategori" name="kategori" 
                                    class="form-select <?php echo isset($errors['kategori']) ? 'input-error' : ''; ?>">
                                <option value="" disabled <?php echo empty($kategori) ? 'selected' : ''; ?>>Pilih kategori...</option>
                                <option value="Fasilitas" <?php echo ($kategori === 'Fasilitas') ? 'selected' : ''; ?>>Fasilitas Kampus</option>
                                <option value="Akademik" <?php echo ($kategori === 'Akademik') ? 'selected' : ''; ?>>Akademik</option>
                                <option value="Layanan" <?php echo ($kategori === 'Layanan') ? 'selected' : ''; ?>>Layanan Mahasiswa</option>
                                <option value="Lainnya" <?php echo ($kategori === 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                            
                            <!-- Pesan Error Validasi Server-Side PHP[cite: 4] -->
                            <?php if (isset($errors['kategori'])): ?>
                                <span class="error-text"><?php echo htmlspecialchars($errors['kategori']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 4. Isi Laporan -->
                    <div class="form-group">
                        <label for="isi" class="form-label">Isi Laporan</label>
                        <div class="form-control-wrap">
                            <textarea id="isi" name="isi" placeholder="Tulis detail laporan atau diskusi Anda di sini..." 
                                      class="form-textarea <?php echo isset($errors['isi']) ? 'input-error' : ''; ?>"><?php echo htmlspecialchars($isi); ?></textarea>
                            
                            <!-- Pesan Error Validasi Server-Side PHP[cite: 4] -->
                            <?php if (isset($errors['isi'])): ?>
                                <span class="error-text"><?php echo htmlspecialchars($errors['isi']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 5. Lampiran (Opsional) -->
                    <div class="form-group">
                        <label class="form-label">Lampiran (Opsional)</label>
                        <div class="form-control-wrap">
                            <div class="file-upload-box">
                                <label class="btn-choose-file" for="lampiran">Choose File</label>
                                <input type="file" id="lampiran" name="lampiran" style="display: none;" onchange="showSelectedFileName(this)">
                                <span id="file-name-text" class="file-name-display">Tidak ada file yang dipilih</span>
                            </div>
                            <p class="file-hint">Format: JPG, PNG, PDF (maks. 5MB)</p>
                            
                            <!-- Pesan Error Validasi Server-Side PHP[cite: 4] -->
                            <?php if (isset($errors['lampiran'])): ?>
                                <span class="error-text"><?php echo htmlspecialchars($errors['lampiran']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Button Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
                    </div>

                </form>
            </div>
        </main>

    </div>

    <!-- Script Tampilan Nama File yang Dipilih -->
    <script>
        function showSelectedFileName(inputElement) {
            var labelDisplay = document.getElementById('file-name-text');
            if (inputElement.files && inputElement.files[0]) {
                labelDisplay.textContent = inputElement.files[0].name;
                labelDisplay.style.color = '#334155';
            } else {
                labelDisplay.textContent = 'Tidak ada file yang dipilih';
                labelDisplay.style.color = '#94a3b8';
            }
        }
    </script>
</body>
</html>