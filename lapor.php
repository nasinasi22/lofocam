<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lapor_jenis = $_POST['lapor_jenis']; // Barang Hilang atau Barang Temuan
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $kategori = $_POST['kategori'];
    $fakultas = isset($_POST['fakultas']) ? $_POST['fakultas'] : ''; // Add fakultas validation
    $tanggal_hilang = !empty($_POST['tanggal_hilang']) ? $_POST['tanggal_hilang'] : null;
    $tanggal_temuan = !empty($_POST['tanggal_temuan']) ? $_POST['tanggal_temuan'] : null;
    $no_hp = trim($_POST['no_hp']);

    // Validasi input tanggal
    if ($lapor_jenis === 'hilang' && empty($tanggal_hilang)) {
        $error = 'Tanggal Hilang harus diisi untuk Barang Hilang.';
    } elseif ($lapor_jenis === 'temuan' && empty($tanggal_temuan)) {
        $error = 'Tanggal Ditemukan harus diisi untuk Barang Temuan.';
    }

    // Validasi fakultas
    if (empty($fakultas)) {
        $error = 'Fakultas harus diisi.';
    }

    // Proses upload file
    $foto_barang = '';
    if (isset($_FILES['foto_barang']) && $_FILES['foto_barang']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto_barang']['tmp_name'];
        $file_name = uniqid() . '-' . $_FILES['foto_barang']['name'];
        $upload_dir = '../uploads/';
        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $foto_barang = $file_name;
        } else {
            $error = 'Gagal mengunggah file.';
        }
    }

    // Jika tidak ada error, simpan ke database
    if (!$error) {
        $status = ($lapor_jenis === 'temuan') ? 'Ditemukan' : 'Belum Ditemukan';
        $sql = "INSERT INTO barang (nama_barang, deskripsi, kategori, fakultas, no_hp, foto_barang, status, tanggal_hilang, tanggal_temuan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Nullify the opposite date
        $tanggal_hilang = ($lapor_jenis === 'hilang') ? $tanggal_hilang : null;
        $tanggal_temuan = ($lapor_jenis === 'temuan') ? $tanggal_temuan : null;

        $stmt->bind_param(
            "sssssssss", 
            $judul, 
            $deskripsi, 
            $kategori, 
            $fakultas, // Use fakultas here
            $no_hp, 
            $foto_barang, 
            $status, 
            $tanggal_hilang, 
            $tanggal_temuan
        );

        if ($stmt->execute()) {
            $success = 'Laporan berhasil dikirim.';
        } else {
            $error = 'Terjadi kesalahan saat menyimpan laporan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Barang</title>
    <link rel="stylesheet" href="../css/lapor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function toggleTanggal() {
            const jenisLaporan = document.getElementById('lapor_jenis').value;
            document.getElementById('tanggal_hilang_group').style.display = (jenisLaporan === 'hilang') ? 'block' : 'none';
            document.getElementById('tanggal_temuan_group').style.display = (jenisLaporan === 'temuan') ? 'block' : 'none';
        }
        document.addEventListener('DOMContentLoaded', toggleTanggal);
    </script>
</head>
<body>
    <!-- Header -->
    <header class="navbar">
    <div class="logo">Lofocam</div>
    <div class="nav-container">
        <ul class="nav-links">
            <li><a href="home.php">Beranda</a></li>
            <li><a href="daftar-barang-hilang.php">Barang Hilang</a></li>
            <li><a href="daftar-barang-temuan.php">Barang Temuan</a></li>
            <li><a href="lapor.php" class="active">Lapor</a></li>
        </ul>
        <div class="icons">
            <a href="#"><i class="fas fa-bell"></i></a>
            <a href="#"><i class="fas fa-user-circle"></i></a>
            <a href="beranda.php" class="btn-logout">Logout</a>
        </div>
    </div>
</header>

    <!-- Main Content -->
    <main class="lapor-form">
        <div class="container">
            <!-- Notifikasi -->
            <?php if ($success): ?>
                <div class="notification success"><?= htmlspecialchars($success) ?></div>
            <?php elseif ($error): ?>
                <div class="notification error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <h1>Lapor Barang</h1>
            <form action="lapor.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="lapor_jenis">Jenis Laporan</label>
                    <select id="lapor_jenis" name="lapor_jenis" onchange="toggleTanggal()" required>
                        <option value="hilang">Barang Hilang</option>
                        <option value="temuan">Barang Temuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" placeholder="Masukkan judul laporan" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Detail Informasi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="5" placeholder="Masukkan detail informasi barang" required></textarea>
                </div>
                <div class="form-group" id="tanggal_hilang_group">
                    <label for="tanggal_hilang">Tanggal Hilang</label>
                    <input type="date" id="tanggal_hilang" name="tanggal_hilang">
                </div>
                <div class="form-group" id="tanggal_temuan_group" style="display:none;">
                    <label for="tanggal_temuan">Tanggal Ditemukan</label>
                    <input type="date" id="tanggal_temuan" name="tanggal_temuan">
                </div>
                <div class="form-group">
                    <label for="kategori">Kategori Barang</label>
                    <select id="kategori" name="kategori" required>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Dompet/Tas">Dompet/Tas</option>
                        <option value="Dokumen">Dokumen</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fakultas">Fakultas</label>
                    <select id="fakultas" name="fakultas" required>
                        <option value="FTI">Fakultas Teknologi Industri</option>
                        <option value="FTSP">Fakultas Teknik Sipil dan Perencanaan</option>
                        <option value="FIAI">Fakultas Ilmu Agama Islam</option>
                        <option value="FH">Fakultas Hukum</option>
                        <option value="FK">Fakultas Kedokteran</option>
                        <option value="FPSB">Fakultas Psikologi dan Sosial Budaya</option>
                        <option value="FEB">Fakultas Ekonomi dan Bisnis</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="no_hp">No HP</label>
                    <input type="tel" id="no_hp" name="no_hp" placeholder="Masukkan nomor HP" required>
                </div>
                <div class="form-group">
                    <label for="foto_barang">Unggah Foto</label>
                    <input type="file" id="foto_barang" name="foto_barang">
                </div>
                <button type="submit" class="btn-primary">Kirim Laporan</button>
            </form>
        </div>
    </main>
</body>
</html>
