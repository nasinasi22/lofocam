<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../config/db.php';

$barang_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = trim($_POST['deskripsi']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO klaim (barang_id, user_id, deskripsi, status) VALUES (?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $barang_id, $user_id, $deskripsi);

    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['success'] = "Klaim berhasil diajukan. Admin akan memverifikasinya.";
        header("Location: daftar-barang-hilang.php");
        exit();
    } else {
        $error = "Terjadi kesalahan saat mengajukan klaim.";
    }
}

// Query to fetch the item details
$sql_barang = "SELECT * FROM barang WHERE id = ?";
$stmt = $conn->prepare($sql_barang);
$stmt->bind_param("i", $barang_id);
$stmt->execute();
$result = $stmt->get_result();
$barang = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klaim Barang Hilang</title>
    <link rel="stylesheet" href="../css/klaim-barang.css">
</head>
<body>
    <!-- Main Content -->
    <div class="container">
        <h1>Klaim Barang Hilang</h1>
        <?php if (isset($error)): ?>
            <p class="alert error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <div class="barang-info">
            <h2><?= htmlspecialchars($barang['nama_barang']) ?></h2>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($barang['kategori']) ?></p>
            <p><strong>Fakultas:</strong> <?= htmlspecialchars($barang['fakultas']) ?></p>
            <p><strong>Deskripsi:</strong> <?= htmlspecialchars($barang['deskripsi']) ?></p>
        </div>
        <form action="klaim-barang-hilang.php?id=<?= $barang_id ?>" method="POST">
            <div class="form-group">
                <label for="deskripsi">Alasan Klaim</label>
                <textarea id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan alasan klaim Anda..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajukan Klaim</button>
        </form>
    </div>
</body>
</html>
