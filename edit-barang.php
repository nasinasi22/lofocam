<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

$nama_barang = $deskripsi = $no_hp = $fakultas = $kategori = $tanggal = "";
$foto_barang = "";
$errors = [];
$editMode = false;

// Jika ada ID untuk edit
if (isset($_GET['id'])) {
    $editMode = true;
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM barang WHERE id = $id");
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $nama_barang = $data['nama_barang'];
        $deskripsi = $data['deskripsi'];
        $no_hp = $data['no_hp'];
        $fakultas = $data['fakultas'];
        $kategori = $data['kategori'];
        $status = $data['status']; // Menyimpan status untuk menentukan tanggal yang ditampilkan
        $foto_barang = $data['foto_barang'];

        // Pilih tanggal yang ditampilkan berdasarkan status barang
        if ($status == 'Ditemukan') {
            $tanggal = $data['tanggal_temuan'];
        } else {
            $tanggal = $data['tanggal_hilang'];
        }
    } else {
        header("Location: kelola-barang.php");
        exit();
    }
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $deskripsi = $_POST['deskripsi'];
    $no_hp = $_POST['no_hp'];
    $fakultas = $_POST['fakultas'];
    $kategori = $_POST['kategori'];
    $status = $_POST['status'];
    $tanggal = $_POST['tanggal']; // Dapatkan tanggal dari form input

    // Upload Foto Barang
    if (!empty($_FILES['foto_barang']['name'])) {
        $target_dir = "../uploads/";
        $foto_barang = time() . '-' . basename($_FILES['foto_barang']['name']);
        $target_file = $target_dir . $foto_barang;
        move_uploaded_file($_FILES['foto_barang']['tmp_name'], $target_file);
    }

    // Insert atau Update
    if ($editMode) {
        if ($status == 'Ditemukan') {
            $query = "UPDATE barang SET nama_barang=?, deskripsi=?, no_hp=?, fakultas=?, kategori=?, tanggal_temuan=?, foto_barang=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssssi", $nama_barang, $deskripsi, $no_hp, $fakultas, $kategori, $tanggal, $foto_barang, $id);
        } else {
            $query = "UPDATE barang SET nama_barang=?, deskripsi=?, no_hp=?, fakultas=?, kategori=?, tanggal_hilang=?, foto_barang=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssssi", $nama_barang, $deskripsi, $no_hp, $fakultas, $kategori, $tanggal, $foto_barang, $id);
        }
    } else {
        // Jika menambah barang baru, set tanggal sesuai statusnya
        if ($status == 'Ditemukan') {
            $query = "INSERT INTO barang (nama_barang, deskripsi, no_hp, fakultas, kategori, tanggal_temuan, foto_barang) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssss", $nama_barang, $deskripsi, $no_hp, $fakultas, $kategori, $tanggal, $foto_barang);
        } else {
            $query = "INSERT INTO barang (nama_barang, deskripsi, no_hp, fakultas, kategori, tanggal_hilang, foto_barang) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssss", $nama_barang, $deskripsi, $no_hp, $fakultas, $kategori, $tanggal, $foto_barang);
        }
    }

    if ($stmt->execute()) {
        header("Location: kelola-barang.php");
        exit();
    } else {
        $errors[] = "Terjadi kesalahan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editMode ? 'Edit' : 'Tambah'; ?> Barang</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header class="navbar">
        <h1><?= $editMode ? 'Edit' : 'Tambah'; ?> Barang</h1>
        <a href="kelola-barang.php" class="btn btn-secondary">Kembali</a>
    </header>
    <main class="container">
        <form method="POST" enctype="multipart/form-data" class="form-container">
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" value="<?= htmlspecialchars($nama_barang); ?>" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3" required><?= htmlspecialchars($deskripsi); ?></textarea>
            </div>
            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="no_hp" value="<?= htmlspecialchars($no_hp); ?>" required>
            </div>
            <div class="form-group">
                <label>Fakultas</label>
                <input type="text" name="fakultas" value="<?= htmlspecialchars($fakultas); ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <input type="text" name="kategori" value="<?= htmlspecialchars($kategori); ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="Hilang" <?= $status == 'Hilang' ? 'selected' : ''; ?>>Hilang</option>
                    <option value="Ditemukan" <?= $status == 'Ditemukan' ? 'selected' : ''; ?>>Ditemukan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal); ?>" required>
            </div>
            <div class="form-group">
                <label>Foto Barang</label>
                <input type="file" name="foto_barang">
                <?php if ($foto_barang): ?>
                    <p>Foto Saat Ini: <?= $foto_barang; ?></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary"><?= $editMode ? 'Simpan Perubahan' : 'Tambah Barang'; ?></button>
        </form>
    </main>
</body>
</html>
