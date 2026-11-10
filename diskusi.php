
<?php
include "koneksi.php";

// Memeriksa status login
if (!isset($_SESSION['Id_User'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['Id_User'];
$role = $_SESSION['Role'];
$nama = $_SESSION['Nama_Lengkap'];

$errors = [];
$success = "";

// Mengambil kategori dari database
$query_kategori = mysqli_query(
    $conn,
    "SELECT * FROM Kategori ORDER BY Nama_Kategori ASC"
);

// PROSES FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // CLIENT: Membuat topik baru
    if (isset($_POST['buat_topik']) && $role == 'Client') {

        $judul = trim($_POST['judul']);
        $kategori = (int) $_POST['kategori'];
        $isi = trim($_POST['isi']);

        // Validasi judul
        if ($judul == '') {
            $errors[] = "Judul topik wajib diisi.";
        } elseif (strlen($judul) < 5 || strlen($judul) > 255) {
            $errors[] = "Judul harus terdiri dari 5-255 karakter.";
        }

        // Validasi kategori
        $cek_kategori = mysqli_prepare(
            $conn,
            "SELECT Id_Kategori FROM Kategori WHERE Id_Kategori = ?"
        );

        mysqli_stmt_bind_param($cek_kategori, "i", $kategori);
        mysqli_stmt_execute($cek_kategori);

        $hasil_kategori = mysqli_stmt_get_result($cek_kategori);

        if ($kategori <= 0 || mysqli_num_rows($hasil_kategori) == 0) {
            $errors[] = "Silakan pilih kategori yang tersedia.";
        }

        // Validasi isi diskusi
        if ($isi == '') {
            $errors[] = "Isi aspirasi wajib diisi.";
        } elseif (strlen($isi) < 10 || strlen($isi) > 10000) {
            $errors[] = "Isi aspirasi harus terdiri dari 10-10000 karakter.";
        }

        // Menyimpan topik
        if (empty($errors)) {

            $query = "INSERT INTO Topik
                (Id_User, Id_Kategori, Judul_Topik, Isi_Diskusi, Status)
                VALUES (?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $query);

            $status = "Menunggu Tanggapan";

            mysqli_stmt_bind_param(
                $stmt,
                "iisss",
                $id_user,
                $kategori,
                $judul,
                $isi,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: diskusi.php?berhasil=topik");
                exit;
            } else {
                $errors[] = "Topik gagal disimpan.";
            }
        }
    }

    // EXPERT: Memberikan tanggapan
    if (isset($_POST['balas_topik']) && $role == 'Expert') {

        $id_topik = (int) $_POST['id_topik'];
        $isi_respon = trim($_POST['isi_respon']);

        if ($id_topik <= 0) {
            $errors[] = "Topik tidak valid.";
        }

        if ($isi_respon == '') {
            $errors[] = "Tanggapan wajib diisi.";
        } elseif (strlen($isi_respon) < 5 || strlen($isi_respon) > 10000) {
            $errors[] = "Tanggapan harus terdiri dari 5-10000 karakter.";
        }

        // Memastikan topik tersedia
        $cek_topik = mysqli_prepare(
            $conn,
            "SELECT Id_Topik FROM Topik WHERE Id_Topik = ?"
        );

        mysqli_stmt_bind_param($cek_topik, "i", $id_topik);
        mysqli_stmt_execute($cek_topik);

        $hasil_topik = mysqli_stmt_get_result($cek_topik);

        if (mysqli_num_rows($hasil_topik) == 0) {
            $errors[] = "Topik tidak ditemukan.";
        }

        // Menyimpan tanggapan Expert
        if (empty($errors)) {

            $query = "INSERT INTO Respon
                (Id_User, Id_Topik, Isi_Respon)
                VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($conn, $query);

            mysqli_stmt_bind_param(
                $stmt,
                "iis",
                $id_user,
                $id_topik,
                $isi_respon
            );

            if (mysqli_stmt_execute($stmt)) {

                // Memperbarui status topik
                $update = mysqli_prepare(
                    $conn,
                    "UPDATE Topik SET Status = 'Sudah Ditanggapi'
                     WHERE Id_Topik = ?"
                );

                mysqli_stmt_bind_param($update, "i", $id_topik);
                mysqli_stmt_execute($update);

                header("Location: diskusi.php?berhasil=respon");
                exit;

            } else {
                $errors[] = "Tanggapan gagal disimpan.";
            }
        }
    }
}

// Pesan keberhasilan
if (isset($_GET['berhasil'])) {
    if ($_GET['berhasil'] == 'topik') {
        $success = "Aspirasi berhasil dikirim!";
    } elseif ($_GET['berhasil'] == 'respon') {
        $success = "Tanggapan berhasil dikirim!";
    }
}

// Mengambil seluruh topik dan informasi pembuatnya
$query_topik = "
    SELECT
        Topik.*,
        User.Nama_Lengkap,
        Kategori.Nama_Kategori
    FROM Topik
    JOIN User ON Topik.Id_User = User.Id_User
    JOIN Kategori ON Topik.Id_Kategori = Kategori.Id_Kategori
    ORDER BY Topik.Created_At DESC
";

$result_topik = mysqli_query($conn, $query_topik);

function aman($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Aspirasi Kampus</title>
    <link rel="stylesheet" href="diskusi.css">
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <div class="brand-icon">A</div>
            <h2>Aspirasi Kampus</h2>
        </div>

        <nav class="menu">
            <a href="dashboard.php">
                <span>⌂</span> Dashboard
            </a>

            <a href="diskusi.php" class="active">
                <span>▤</span> Laporan
            </a>

            <a href="riwayat.php">
                <span>◷</span> Riwayat Laporan
            </a>
        </nav>

        <a href="logout.php" class="logout">
            <span>⇥</span> Keluar
        </a>

    </aside>


    <!-- BAGIAN UTAMA -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <span>Halaman Laporan</span>

            <div class="profile">
                <div class="avatar">
                    <?php echo strtoupper(substr($nama, 0, 1)); ?>
                </div>
                <div>
                    <strong><?php echo aman($nama); ?></strong>
                    <small><?php echo aman($role); ?></small>
                </div>
            </div>
        </header>


        <section class="content">

            <div class="page-heading">
                <h1>
                    <?php if ($role == 'Client') { ?>
                        Buat Laporan Aspirasi
                    <?php } else { ?>
                        Forum Diskusi Aspirasi
                    <?php } ?>
                </h1>

                <p>
                    Sampaikan aspirasi dan berikan tanggapan
                    untuk kampus yang lebih baik.
                </p>
            </div>


            <!-- PESAN BERHASIL -->
            <?php if ($success != '') { ?>
                <div class="success">
                    <?php echo aman($success); ?>
                </div>
            <?php } ?>


            <!-- PESAN ERROR -->
            <?php if (!empty($errors)) { ?>
                <div class="error">
                    <?php foreach ($errors as $error) { ?>
                        <p><?php echo aman($error); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>


            <!-- FORM CLIENT -->
            <?php if ($role == 'Client') { ?>

            <div class="form-card">

                <div class="card-heading">
                    <div>
                        <h2>Form Aspirasi</h2>
                        <p>Isi formulir berikut untuk mengirimkan aspirasi.</p>
                    </div>
                    <span class="heading-icon">✎</span>
                </div>

                <form method="POST">

                    <div class="form-group">
                        <label>Judul Laporan</label>
                        <input
                            type="text"
                            name="judul"
                            placeholder="Masukkan judul aspirasi..."
                            value="<?php echo isset($_POST['judul']) ? aman($_POST['judul']) : ''; ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label>Kategori Aspirasi</label>

                        <select name="kategori">
                            <option value="">Pilih kategori...</option>

                            <?php while ($kategori = mysqli_fetch_assoc($query_kategori)) { ?>

                                <option value="<?php echo $kategori['Id_Kategori']; ?>"
                                    <?php
                                    if (
                                        isset($_POST['kategori']) &&
                                        $_POST['kategori'] == $kategori['Id_Kategori']
                                    ) {
                                        echo "selected";
                                    }
                                    ?>>
                                    <?php echo aman($kategori['Nama_Kategori']); ?>
                                </option>

                            <?php } ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Isi Laporan</label>
                        <textarea
                            name="isi"
                            placeholder="Tuliskan detail aspirasi, kritik, atau saran Anda..."
                        ><?php echo isset($_POST['isi']) ? aman($_POST['isi']) : ''; ?></textarea>
                    </div>

                    <div class="form-footer">
                        <span>Pastikan informasi yang diberikan jelas dan sopan.</span>
                        <button type="submit" name="buat_topik">
                            Kirim Aspirasi →
                        </button>
                    </div>

                </form>

            </div>

            <?php } ?>


            <!-- DAFTAR DISKUSI -->
            <div class="discussion-heading">
                <div>
                    <h2>Daftar Topik Diskusi</h2>
                    <p>Lihat aspirasi yang disampaikan oleh mahasiswa.</p>
                </div>
            </div>


            <?php if (mysqli_num_rows($result_topik) > 0) { ?>

                <?php while ($topik = mysqli_fetch_assoc($result_topik)) { ?>

                    <div class="topic-card">

                        <div class="topic-top">
                            <span class="topic-category">
                                <?php echo aman($topik['Nama_Kategori']); ?>
                            </span>

                            <span class="topic-status">
                                <?php echo aman($topik['Status']); ?>
                            </span>
                        </div>

                        <h3>
                            <?php echo aman($topik['Judul_Topik']); ?>
                        </h3>

                        <div class="topic-info">
                            Oleh <?php echo aman($topik['Nama_Lengkap']); ?>
                            ·
                            <?php echo date('d-m-Y H:i', strtotime($topik['Created_At'])); ?>
                        </div>

                        <p class="topic-content">
                            <?php echo nl2br(aman($topik['Isi_Diskusi'])); ?>
                        </p>


                        <?php
                        // Mengambil tanggapan dari topik terkait
                        $id_topik = $topik['Id_Topik'];

                        $query_respon = "
                            SELECT Respon.*, User.Nama_Lengkap
                            FROM Respon
                            JOIN User ON Respon.Id_User = User.Id_User
                            WHERE Respon.Id_Topik = ?
                            ORDER BY Respon.Created_At ASC
                        ";

                        $stmt_respon = mysqli_prepare($conn, $query_respon);
                        mysqli_stmt_bind_param($stmt_respon, "i", $id_topik);
                        mysqli_stmt_execute($stmt_respon);

                        $result_respon = mysqli_stmt_get_result($stmt_respon);
                        ?>

                        <div class="response-area">

                            <h4>Tanggapan Expert</h4>

                            <?php if (mysqli_num_rows($result_respon) > 0) { ?>

                                <?php while ($respon = mysqli_fetch_assoc($result_respon)) { ?>

                                    <div class="response-card">

                                        <div class="expert-name">
                                            <span class="expert-avatar">E</span>

                                            <strong>
                                                <?php echo aman($respon['Nama_Lengkap']); ?>
                                            </strong>

                                            <span class="expert-label">Expert</span>
                                        </div>

                                        <p>
                                            <?php echo nl2br(aman($respon['Isi_Respon'])); ?>
                                        </p>

                                        <small>
                                            <?php echo date('d-m-Y H:i', strtotime($respon['Created_At'])); ?>
                                        </small>

                                    </div>

                                <?php } ?>

                            <?php } else { ?>

                                <p class="empty-response">
                                    Belum ada tanggapan dari Expert.
                                </p>

                            <?php } ?>


                            <!-- FORM BALASAN EXPERT -->
                            <?php if ($role == 'Expert') { ?>

                                <form method="POST" class="reply-form">

                                    <input
                                        type="hidden"
                                        name="id_topik"
                                        value="<?php echo $id_topik; ?>"
                                    >

                                    <textarea
                                        name="isi_respon"
                                        placeholder="Tuliskan tanggapan resmi..."
                                    ></textarea>

                                    <button type="submit" name="balas_topik">
                                        Kirim Tanggapan →
                                    </button>

                                </form>

                            <?php } ?>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="empty-data">
                    <h3>Belum Ada Topik Aspirasi</h3>
                    <p>Topik aspirasi yang dikirimkan akan muncul di sini.</p>
                </div>

            <?php } ?>

        </section>

    </main>

</div>

</body>
</html>
