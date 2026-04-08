<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

// Hapus barang jika ada request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM barang WHERE id = $id");
    header("Location: kelola-barang.php");
    exit();
}

// Ambil semua data barang, memilih tanggal berdasarkan status
$result = $conn->query("SELECT *, 
                               CASE 
                                   WHEN status = 'Ditemukan' THEN tanggal_temuan 
                                   ELSE tanggal_hilang 
                               END AS tanggal_terkait
                        FROM barang");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Barang</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header class="navbar">
        <h1>Lofocam Admin - Kelola Barang</h1>
        <a href="home-admin.php" class="btn">Kembali ke Dashboard</a>
    </header>
    <main class="container">
        <h2>Daftar Barang</h2>
        <a href="edit-barang.php" class="btn btn-primary">Tambah Barang</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Fakultas</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['kategori']); ?></td>
                        <td><?= htmlspecialchars($row['fakultas']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_terkait']); ?></td>
                        <td>
                            <a href="edit-barang.php?id=<?= $row['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="?delete=<?= $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
