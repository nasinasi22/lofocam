<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../PHP/login.php");
    exit();
}
include '../config/db.php';

$sql = "SELECT * FROM barang ORDER BY tanggal_hilang DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <script src="../JS/dashboard.js" defer></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard Admin</h1>
            <a href="../PHP/logoutSubmit.php" class="btn-logout">Logout</a>
        </header>
        <main>
            <h2>Daftar Barang</h2>
            <a href="tambah-barang.php" class="btn">Tambah Barang</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Barang</th>
                        <th>Fakultas</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nama_barang'] ?></td>
                            <td><?= $row['fakultas'] ?></td>
                            <td><?= $row['kategori'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td>
                                <a href="ubah-barang.php?id=<?= $row['id'] ?>" class="btn">Edit</a>
                                <a href="hapus-barang.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
