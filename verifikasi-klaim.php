<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

// Update status klaim jika tombol ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $klaim_id = intval($_POST['klaim_id']);
    $status = $_POST['status'];
    $update = $conn->prepare("UPDATE klaim SET status = ? WHERE id = ?");
    $update->bind_param("si", $status, $klaim_id);
    $update->execute();
    header("Location: verifikasi-klaim.php?message=success");
    exit();
}

// Ambil data klaim yang pending
$result = $conn->query("SELECT k.id, k.deskripsi, k.status, b.nama_barang, u.username 
    FROM klaim k 
    JOIN barang b ON k.barang_id = b.id 
    JOIN users u ON k.user_id = u.id 
    WHERE k.status = 'Pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Klaim</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header class="navbar">
        <h1>Lofocam Admin</h1>
        <a href="home-admin.php" class="btn btn-success">Dashboard</a>
    </header>
    <div class="container">
        <h2>Verifikasi Klaim</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Username</th>
                    <th>Deskripsi Klaim</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="klaim_id" value="<?= $row['id']; ?>">
                                <button type="submit" name="status" value="Approved" class="btn btn-success">Approve</button>
                                <button type="submit" name="status" value="Rejected" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
