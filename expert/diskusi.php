<?php
require_once __DIR__ . "/../client/validasi.php";

// Cek login + pastikan role expert
$user = wajibLogin($connect, "expert");
$id_expert = (int) $user["Id_User"];

// 1. PROSES TAMBAH TANGGAPAN BARU & OTOMATIS UBAH STATUS JADI 'Selesai'
if (isset($_POST["kirim_respon"])) {
    $id_topik   = (int) ($_POST["id_topik"] ?? 0);
    $isi_respon = trim($_POST["isi_respon"] ?? "");

    if ($id_topik > 0 && $isi_respon !== "") {
        $stmt_ins = mysqli_prepare($connect, "INSERT INTO respon (Id_User, Id_Topik, Isi_Respon) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_ins, "iis", $id_expert, $id_topik, $isi_respon);
        mysqli_stmt_execute($stmt_ins);

        // Sesuaikan dengan standar status temanmu ('Selesai') agar langsung hijau di beranda client
        $status_baru = "Selesai";
        $stmt_status = mysqli_prepare($connect, "UPDATE topik SET Status = ? WHERE Id_Topik = ?");
        mysqli_stmt_bind_param($stmt_status, "si", $status_baru, $id_topik);
        mysqli_stmt_execute($stmt_status);

        header("Location: diskusi.php?berhasil=tambah#topik-" . $id_topik);
        exit();
    }
}

// 2. PROSES EDIT TANGGAPAN (Hanya bisa mengedit tanggapan milik sendiri)
if (isset($_POST["simpan_edit_respon"])) {
    $id_respon  = (int) ($_POST["id_respon"] ?? 0);
    $id_topik   = (int) ($_POST["id_topik"] ?? 0);
    $isi_respon = trim($_POST["isi_respon"] ?? "");

    if ($id_respon > 0 && $isi_respon !== "") {
        $stmt_upd = mysqli_prepare($connect, "UPDATE respon SET Isi_Respon = ? WHERE Id_Respon = ? AND Id_User = ?");
        mysqli_stmt_bind_param($stmt_upd, "sii", $isi_respon, $id_respon, $id_expert);
        mysqli_stmt_execute($stmt_upd);

        header("Location: diskusi.php?berhasil=edit#topik-" . $id_topik);
        exit();
    }
}

// 3. PROSES HAPUS TANGGAPAN (Hanya bisa menghapus tanggapan milik sendiri)
if (isset($_POST["hapus_respon"])) {
    $id_respon = (int) ($_POST["id_respon"] ?? 0);
    $id_topik  = (int) ($_POST["id_topik"] ?? 0);

    if ($id_respon > 0) {
        $stmt_del = mysqli_prepare($connect, "DELETE FROM respon WHERE Id_Respon = ? AND Id_User = ?");
        mysqli_stmt_bind_param($stmt_del, "ii", $id_respon, $id_expert);
        mysqli_stmt_execute($stmt_del);

        // Cek sisa tanggapan pada topik ini; jika kosong (0), kembalikan status ke 'Terbuka'
        if ($id_topik > 0) {
            $stmt_cek = mysqli_prepare($connect, "SELECT COUNT(*) AS total FROM respon WHERE Id_Topik = ?");
            mysqli_stmt_bind_param($stmt_cek, "i", $id_topik);
            mysqli_stmt_execute($stmt_cek);
            $res_cek   = mysqli_stmt_get_result($stmt_cek);
            $data_cek  = mysqli_fetch_assoc($res_cek);

            if ((int) ($data_cek["total"] ?? 0) === 0) {
                $status_kembali = "Terbuka";
                $stmt_status = mysqli_prepare($connect, "UPDATE topik SET Status = ? WHERE Id_Topik = ?");
                mysqli_stmt_bind_param($stmt_status, "si", $status_kembali, $id_topik);
                mysqli_stmt_execute($stmt_status);
            }
        }

        header("Location: diskusi.php?berhasil=hapus#topik-" . $id_topik);
        exit();
    }
}

// Cek apakah sedang membuka mode edit untuk tanggapan tertentu
$edit_id = isset($_GET["edit"]) ? (int) $_GET["edit"] : 0;

// Ambil semua topik diskusi masuk beserta data pengirim & kategorinya
$query_topik = mysqli_query($connect, "
    SELECT t.*, u.Username, k.Nama_Kategori 
    FROM topik t
    LEFT JOIN user u ON t.Id_User = u.Id_User
    LEFT JOIN kategori k ON t.Id_Kategori = k.Id_Kategori
    ORDER BY t.Id_Topik DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RembugUTM - Laporan & Tanggapan Expert</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="../index.php" class="logo">
            <span class="logo-icon">R</span> Rembug<b>UTM</b>
        </a>

        <nav class="menu">
            <a href="beranda.php">
                <i class="fa-solid fa-house"></i> <span>Beranda</span>
            </a>
            <a href="diskusi.php" class="active">
                <i class="fa-solid fa-comments"></i> <span>Laporan</span>
            </a>
            <a href="profil.php">
                <i class="fa-solid fa-user-gear"></i> <span>Profil Saya</span>
            </a>
        </nav>

        <a href="../auth/logout.php" class="logout">
            <span>Keluar</span>
        </a>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-title">
                <a href="../index.php" class="logo logo-dark">
                    <span class="logo-icon">R</span> Rembug<b>UTM</b>
                </a>
            </div>

            <div class="profile" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 14px; font-weight: 600;">
                    <?= htmlspecialchars($user['username'] ?? 'Expert'); ?>
                </span>
                <a href="profil.php" class="avatar" title="Lihat Profil">
                    <?= strtoupper(substr(htmlspecialchars($user['username'] ?? 'E'), 0, 1)); ?>
                </a>
            </div>
        </header>

        <section class="content">
            <div class="form-header" style="margin-bottom: 20px;">
                <div class="header-icon">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <div class="header-text">
                    <h1>Laporan & Diskusi Masuk</h1>
                    <p>Tinjau laporan dari masyarakat dan berikan tanggapan resmi.</p>
                </div>
            </div>

            <?php if (isset($_GET['berhasil'])): ?>
                <?php
                    $pesan_sukses = "Tanggapan berhasil dikirim.";
                    if ($_GET['berhasil'] === 'edit')  $pesan_sukses = "Tanggapan berhasil diperbarui.";
                    if ($_GET['berhasil'] === 'hapus') $pesan_sukses = "Tanggapan berhasil dihapus.";
                ?>
                <div style="background-color: #dcfce7; border-left: 4px solid #22c55e; color: #15803d; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 14px;">
                    <?= $pesan_sukses; ?>
                </div>
            <?php endif; ?>

            <!-- DAFTAR TOPIK MASUK -->
            <?php if ($query_topik && mysqli_num_rows($query_topik) > 0): ?>
                <?php while ($topik = mysqli_fetch_assoc($query_topik)): ?>
                    <?php
                        $id_topik_cur  = (int) $topik['Id_Topik'];
                        $status_cur    = $topik['Status'] ?? 'Terbuka';
                        $sudah_dijawab = ($status_cur === 'Selesai');

                        // Ambil semua tanggapan dari seluruh expert untuk topik ini
                        $query_respon = mysqli_query($connect, "
                            SELECT r.*, u.Username 
                            FROM respon r
                            LEFT JOIN user u ON r.Id_User = u.Id_User
                            WHERE r.Id_Topik = $id_topik_cur
                            ORDER BY r.Created_At ASC
                        ");
                    ?>
                    <div id="topik-<?= $id_topik_cur; ?>" class="form-container" style="margin-bottom: 24px;">
                        <!-- HEADER TOPIK -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <span style="font-size: 12px; color: #64748b;">
                                    Oleh: <b><?= htmlspecialchars($topik['Username'] ?? 'Anonim'); ?></b> &bull;
                                    Kategori: <b><?= htmlspecialchars($topik['Nama_Kategori'] ?? '-'); ?></b> &bull;
                                    Dibuat: <?= date('d M Y, H:i', strtotime($topik['Created_At'])); ?>
                                </span>
                                <h3 style="margin: 6px 0 0 0; font-size: 18px;">
                                    <?= htmlspecialchars($topik['Judul_Topik']); ?>
                                </h3>
                            </div>

                            <!-- BADGE STATUS OTOMATIS -->
                            <span style="padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap;
                                background: <?= $sudah_dijawab ? '#dcfce7' : '#e0f2fe'; ?>;
                                color: <?= $sudah_dijawab ? '#15803d' : '#0369a1'; ?>;">
                                <?= $sudah_dijawab ? 'Telah Ditanggapi' : htmlspecialchars($status_cur); ?>
                            </span>
                        </div>

                        <!-- ISI DISKUSI -->
                        <p style="color: #334155; font-size: 14px; line-height: 1.6; margin-bottom: 14px;">
                            <?= nl2br(htmlspecialchars($topik['Isi_Diskusi'])); ?>
                        </p>

                        <!-- LAMPIRAN (DETEKSI FOLDER OTOMATIS) -->
                        <?php if (!empty($topik['Lampiran'])): ?>
                            <?php
                                $nama_file = $topik['Lampiran'];
                                $lokasi_cek = [
                                    "../uploads/" . $nama_file,
                                    "../client/uploads/" . $nama_file,
                                    "../assets/uploads/" . $nama_file,
                                    "../img/" . $nama_file,
                                    "../images/" . $nama_file
                                ];

                                $url_lampiran = "../uploads/" . $nama_file;
                                foreach ($lokasi_cek as $path) {
                                    if (file_exists(__DIR__ . "/" . $path)) {
                                        $url_lampiran = $path;
                                        break;
                                    }
                                }
                            ?>
                            <div style="margin-bottom: 16px;">
                                <a href="<?= htmlspecialchars($url_lampiran); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #2563eb; text-decoration: none; background: #eff6ff; padding: 6px 12px; border-radius: 6px;">
                                    <i class="fa-solid fa-paperclip"></i> Lihat Lampiran (<?= htmlspecialchars($nama_file); ?>)
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- DAFTAR TANGGAPAN DARI PARA EXPERT -->
                        <?php if ($query_respon && mysqli_num_rows($query_respon) > 0): ?>
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 16px;">
                                <strong style="display: block; font-size: 13px; color: #0f172a; margin-bottom: 12px;">
                                    <i class="fa-solid fa-comments"></i> Tanggapan Expert (<?= mysqli_num_rows($query_respon); ?>):
                                </strong>

                                <?php while ($resp = mysqli_fetch_assoc($query_respon)): ?>
                                    <?php
                                        $id_resp_cur   = (int) $resp['Id_Respon'];
                                        $milik_sendiri = ((int) $resp['Id_User'] === $id_expert);
                                        $sudah_diedit  = (!empty($resp['Updated_At']) && $resp['Updated_At'] !== $resp['Created_At']);
                                    ?>
                                    <div style="border-left: 3px solid <?= $milik_sendiri ? '#2563eb' : '#94a3b8'; ?>; padding-left: 12px; margin-bottom: 14px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                                            <span style="font-size: 12px; color: #64748b;">
                                                <b><?= htmlspecialchars($resp['Username'] ?? 'Expert'); ?></b>
                                                <?= $milik_sendiri ? '<span style="background:#dbeafe; color:#1d4ed8; padding:1px 6px; border-radius:4px; font-size:11px; margin-left:4px;">Anda</span>' : ''; ?>
                                                &bull; <?= date('d M Y, H:i', strtotime($resp['Created_At'])); ?>
                                                <?php if ($sudah_diedit): ?>
                                                    <span style="font-style: italic; color: #94a3b8;">
                                                        (Diedit: <?= date('d M Y, H:i', strtotime($resp['Updated_At'])); ?>)
                                                    </span>
                                                <?php endif; ?>
                                            </span>

                                            <!-- TOMBOL EDIT & HAPUS (Hanya tampil jika tanggapan milik expert yang login) -->
                                            <?php if ($milik_sendiri && $edit_id !== $id_resp_cur): ?>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <a href="diskusi.php?edit=<?= $id_resp_cur; ?>#topik-<?= $id_topik_cur; ?>" style="font-size: 12px; color: #2563eb; text-decoration: none;">
                                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                                    </a>

                                                    <form method="POST" action="diskusi.php" style="margin: 0; display: inline;" onsubmit="return confirm('Yakin ingin menghapus tanggapan ini?');">
                                                        <input type="hidden" name="id_respon" value="<?= $id_resp_cur; ?>">
                                                        <input type="hidden" name="id_topik" value="<?= $id_topik_cur; ?>">
                                                        <button type="submit" name="hapus_respon" style="background: none; border: none; padding: 0; font-size: 12px; color: #dc2626; cursor: pointer; font-family: inherit;">
                                                            <i class="fa-solid fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($milik_sendiri && $edit_id === $id_resp_cur): ?>
                                            <!-- FORM INLINE EDIT TANGGAPAN -->
                                            <form method="POST" action="diskusi.php" style="margin-top: 8px;">
                                                <input type="hidden" name="id_respon" value="<?= $id_resp_cur; ?>">
                                                <input type="hidden" name="id_topik" value="<?= $id_topik_cur; ?>">
                                                <textarea name="isi_respon" rows="3" style="width: 100%; padding: 8px; border: 1px solid #93c5fd; border-radius: 6px; font-family: inherit; margin-bottom: 8px;" required><?= htmlspecialchars($resp['Isi_Respon']); ?></textarea>
                                                <div style="display: flex; gap: 8px;">
                                                    <button type="submit" name="simpan_edit_respon" class="btn" style="padding: 6px 14px; font-size: 13px;">Simpan Perubahan</button>
                                                    <a href="diskusi.php#topik-<?= $id_topik_cur; ?>" class="btn btn-outline-cancel" style="padding: 6px 14px; font-size: 13px; text-decoration: none;">Batal</a>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <p style="margin: 6px 0 0 0; font-size: 13.5px; color: #1e293b; line-height: 1.5;">
                                                <?= nl2br(htmlspecialchars($resp['Isi_Respon'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>

                        <!-- FORM TAMBAH TANGGAPAN BARU (Semua expert bisa ikut menanggapi) -->
                        <form method="POST" action="diskusi.php" style="border-top: 1px solid #e2e8f0; padding-top: 14px;">
                            <input type="hidden" name="id_topik" value="<?= $id_topik_cur; ?>">

                            <div style="margin-bottom: 10px;">
                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Tambah Tanggapan:</label>
                                <textarea name="isi_respon" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit;" placeholder="Tulis tanggapan atau masukan Anda untuk topik ini..." required></textarea>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" name="kirim_respon" class="btn">Kirim Tanggapan</button>
                            </div>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="form-container" style="text-align: center; padding: 40px;">
                    <p style="color: #64748b;">Belum ada topik diskusi atau laporan yang masuk.</p>
                </div>
            <?php endif; ?>

        </section>

    </main>

</div>

</body>
</html>