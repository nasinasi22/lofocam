<?php
session_start();
require_once '../config/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil id laporan yang ingin diedit
if (isset($_GET['id'])) {
    $barang_id = intval($_GET['id']);

    // Ambil data laporan berdasarkan id dan user_id
    $sql = "SELECT * FROM barang WHERE id = ? AND no_hp = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $barang_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: riwayat-laporan.php");
        exit();
    }

    $row = $result->fetch_assoc();
} else {
    header("Location: riwayat-laporan.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lapor_jenis = $_POST['lapor_jenis']; // Barang Hilang atau Barang Temuan
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $kategori = $_POST['kategori'];
    $fakultas = $_POST['fakultas'];
    $tanggal_hilang = !empty($_POST['tanggal_hilang']) ? $_POST['tanggal_hilang'] : null;
    $tanggal_temuan = !empty($_POST['tanggal_temuan']) ? $_POST['tanggal_temuan'] : null;
    $no_hp = trim($_POST['no_hp']);

    // Validasi input tanggal
    if ($lapor_jenis === 'hilang' && empty($tanggal_hilang)) {
        $error = 'Tanggal Hilang harus diisi untuk Barang Hilang.';
    } elseif ($lapor_jenis === 'temuan' && empty($tanggal_temuan)) {
        $error = 'Tanggal Ditemukan harus diisi untuk Barang Temuan.';
    }

    // Proses upload file
    $foto_barang = $row['foto_barang']; // Menggunakan foto yang lama jika tidak ada file baru
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

    // Jika tidak ada error, update data ke database
    if (!$error) {
        $status = ($lapor_jenis === 'temuan') ? 'Ditemukan' : 'Belum Ditemukan';
        $sql = "UPDATE barang SET nama_barang = ?, deskripsi = ?, kategori = ?, fakultas = ?, no_hp = ?, foto_barang = ?, status = ?, tanggal_hilang = ?, tanggal_temuan = ? 
                WHERE id = ? AND no_hp = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssi", 
            $judul, 
            $deskripsi, 
            $kategori, 
            $fakultas, 
            $no_hp, 
            $foto_barang, 
            $status, 
            $tanggal_hilang, 
            $tanggal_temuan, 
            $barang_id,
            $user_id
        );

        if ($stmt->execute()) {
            $success = 'Laporan berhasil diperbarui.';
        } else {
            $error = 'Terjadi kesalahan saat memperbarui laporan.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan | Lofocam</title>
    <link rel="stylesheet" href="../css/edit-laporan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">Lofocam</div>
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="home.php">Beranda</a></li>
                <li><a href="riwayat-laporan.php">Riwayat Laporan</a></li>
                <li><a href="lapor.php">Lapor</a></li>
            </ul>
            <div class="icons">
                <a href="#"><i class="fas fa-bell"></i></a>
                <a href="#"><i class="fas fa-user-circle"></i></a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="edit-lapor-form">
        <div class="container">
            <h1>Edit Laporan</h1>

            <!-- Notifikasi -->
            <?php if ($success): ?>
                <div class="notification success"><?= htmlspecialchars($success) ?></div>
            <?php elseif ($error): ?>
                <div class="notification error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="edit-laporan.php?id=<?= $row['id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="lapor_jenis">Jenis Laporan</label>
                    <select id="lapor_jenis" name="lapor_jenis" required>
                        <option value="hilang" <?= $row['tanggal_hilang'] ? 'selected' : '' ?>>Barang Hilang</option>
                        <option value="temuan" <?= $row['tanggal_temuan'] ? 'selected' : '' ?>>Barang Temuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($row['nama_barang']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Detail Informasi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="5" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="tanggal_hilang">Tanggal Hilang</label>
                    <input type="date" id="tanggal_hilang" name="tanggal_hilang" value="<?= htmlspecialchars($row['tanggal_hilang']) ?>">
                </div>
                <div class="form-group">
                    <label for="tanggal_temuan">Tanggal Ditemukan</label>
                    <input type="date" id="tanggal_temuan" name="tanggal_temuan" value="<?= htmlspecialchars($row['tanggal_temuan']) ?>">
                </div>
                <div class="form-group">
                    <label for="kategori">Kategori Barang</label>
                    <select id="kategori" name="kategori" required>
                        <option value="Aksesoris" <?= $row['kategori'] === 'Aksesoris' ? 'selected' : '' ?>>Aksesoris</option>
                        <option value="Elektronik" <?= $row['kategori'] === 'Elektronik' ? 'selected' : '' ?>>Elektronik</option>
                        <option value="Dompet/Tas" <?= $row['kategori'] === 'Dompet/Tas' ? 'selected' : '' ?>>Dompet/Tas</option>
                        <option value="Dokumen" <?= $row['kategori'] === 'Dokumen' ? 'selected' : '' ?>>Dokumen</option>
                        <option value="Lain-lain" <?= $row['kategori'] === 'Lain-lain' ? 'selected' : '' ?>>Lain-lain</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fakultas">Fakultas</label>
                    <select id="fakultas" name="fakultas" required>
                        <option value="FTI" <?= $row['fakultas'] === 'FTI' ? 'selected' : '' ?>>Fakultas Teknologi Industri</option>
                        <option value="FTSP" <?= $row['fakultas'] === 'FTSP' ? 'selected' : '' ?>>Fakultas Teknik Sipil dan Perencanaan</option>
                        <option value="FIAI" <?= $row['fakultas'] === 'FIAI' ? 'selected' : '' ?>>Fakultas Ilmu Agama Islam</option>
                        <option value="FH" <?= $row['fakultas'] === 'FH' ? 'selected' : '' ?>>Fakultas Hukum</option>
                        <option value="FK" <?= $row['fakultas'] === 'FK' ? 'selected' : '' ?>>Fakultas Kedokteran</option>
                        <option value="FPSB" <?= $row['fakultas'] === 'FPSB' ? 'selected' : '' ?>>Fakultas Psikologi dan Sosial Budaya</option>
                        <option value="FEB" <?= $row['fakultas'] === 'FEB' ? 'selected' : '' ?>>Fakultas Ekonomi dan Bisnis</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="no_hp">No HP</label>
                    <input type="tel" id="no_hp" name="no_hp" value="<?= htmlspecialchars($row['no_hp']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="foto_barang">Unggah Foto (Jika ingin mengganti)</label>
                    <input type="file" id="foto_barang" name="foto_barang">
                </div>
                <button type="submit" class="btn-primary">Update Laporan</button>
            </form>
        </div>
    </main>
</body>
</html>
