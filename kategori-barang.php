<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'Semua';

// Query data barang berdasarkan kategori
if ($kategori === 'Semua') {
    $sql = "SELECT * FROM barang";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT * FROM barang WHERE kategori = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $kategori);
    $stmt->execute();
    $result = $stmt->get_result();
}

$username = $_SESSION['username']; // Ambil nama pengguna dari session
?>

<!DOCTYPE HTML>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Barang - <?= htmlspecialchars($kategori); ?></title>
    <link rel="stylesheet" href="../css/home.css"> <!-- Header dan Footer CSS -->
    <link rel="stylesheet" href="../css/kategori-barang.css"> <!-- CSS untuk konten kategori -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">Lofocam</div>
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="home.php">Beranda</a></li>
                <li><a href="daftar-barang.php">Daftar Laporan</a></li>
                <li><a href="riwayat-laporan.php">Riwayat Laporan</a></li>
                <li><a href="lapor.php">Lapor</a></li>
            </ul>
            <div class="icons">
                <a href="#"><i class="fas fa-bell"></i></a>
                <a href="#"><i class="fas fa-user-circle"></i></a>
                <a href="beranda.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <!-- Content -->
    <section class="content">
        <div class="container">
            <h1>Kategori Barang: <?= htmlspecialchars($kategori); ?></h1>
            <div class="barang-list">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="barang-item">
                            <div class="barang-image">
                                <?php 
                                $foto_path = "../uploads/" . htmlspecialchars($row['foto_barang']);
                                $real_path = realpath(__DIR__ . "/../uploads/" . $row['foto_barang']);
                                
                                // Cek apakah file gambar ada
                                if (!empty($row['foto_barang']) && $real_path && file_exists($real_path)): ?>
                                    <img src="<?= $foto_path ?>" alt="Foto Barang">
                                <?php else: ?>
                                    <img src="../images/no-image.jpg" alt="Tidak Ada Foto">
                                <?php endif; ?>
                            </div>
                            <div class="barang-details">
                                <h3><?= htmlspecialchars($row['nama_barang']); ?></h3>
                                <p><strong>Kategori:</strong> <?= htmlspecialchars($row['kategori']); ?></p>
                                <p><strong>Tempat Temuan:</strong> <?= htmlspecialchars($row['fakultas']); ?></p>
                                
                                <?php if ($row['tanggal_temuan']): ?>
                                    <p><strong>Tanggal Temuan:</strong> <?= htmlspecialchars($row['tanggal_temuan']); ?></p>
                                    <p><?= htmlspecialchars($row['deskripsi']); ?></p>
                                    <a href="klaim-barang-temuan.php?id=<?= $row['id']; ?>" class="btn-claim">KLAIM</a>
                                <?php elseif ($row['tanggal_hilang']): ?>
                                    <p><strong>Tanggal Hilang:</strong> <?= htmlspecialchars($row['tanggal_hilang']); ?></p>
                                    <p><?= htmlspecialchars($row['deskripsi']); ?></p>
                                    <a href="klaim-barang-hilang.php?id=<?= $row['id']; ?>" class="btn-claim">KLAIM</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-data">Tidak ada barang dalam kategori ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Lofocam - Lost and Found Campus</p>
            <ul class="social-icons">
                <li><a href="#"><i class="fab fa-facebook"></i></a></li>
                <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
            </ul>
        </div>
    </footer>
</body>
</html>
<?php
$conn->close();
?>
