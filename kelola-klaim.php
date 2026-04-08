<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

// Update status klaim
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = $_GET['action'] === 'approve' ? 'Approved' : 'Rejected';
    $conn->query("UPDATE klaim SET status = '$status' WHERE id = $id");
    header("Location: kelola-klaim.php");
    exit();
}

// Ambil semua data klaim
$result = $conn->query("SELECT klaim.*, users.username, barang.nama_barang 
                        FROM klaim 
                        JOIN users ON klaim.user_id = users.id 
                        JOIN barang ON klaim.barang_id = barang.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Klaim</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header class="navbar">
        <h1>Lofocam Admin - Verifikasi Klaim</h1>
        <a href="home-admin.php" class="btn">Kembali ke Dashboard</a>
    </header>
    <main class="container">
        <h2>Daftar Klaim</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Barang</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                        <td><?= $row['status']; ?></td>
                        <td>
                            <?php if ($row['status'] === 'Pending'): ?>
                                <a href="?action=approve&id=<?= $row['id']; ?>" class="btn btn-success">Approve</a>
                                <a href="?action=reject&id=<?= $row['id']; ?>" class="btn btn-danger">Reject</a>
                            <?php else: ?>
                                <?= $row['status']; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
