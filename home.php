<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username']; // Ambil nama pengguna dari session
include '../config/db.php'; // Include database connection

// Query to get the latest "Ditemukan" barang
$query = "SELECT * FROM barang WHERE status = 'Ditemukan' ORDER BY created_at DESC LIMIT 4"; // Limit to the latest 4 items
$result = $conn->query($query);

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Lofocam - Lost & Found</title>
    <meta name="description" content="Website ini mempermudah pendataan barang hilang dan ditemukan di PT KAI DAOP 2 Bandung.">
    <meta name="keywords" content="Lost and Found, PT KAI, Barang Hilang">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">Lofocam</div> <!-- Logo di kiri -->
        <div class="nav-container"> <!-- Navbar Container -->
            <ul class="nav-links">
                <li><a href="home.php" class="active">Beranda</a></li>
                <li><a href="daftar-barang.php">Daftar Laporan</a></li>
                <li><a href="riwayat-laporan.php">Riwayat Laporan</a></li>
                <li><a href="lapor.php">Lapor</a></li>
            </ul>
            <div class="icons"> <!-- Bagian ikon dan tombol logout -->
                <a href="#"><i class="fas fa-bell"></i></a>
                <a href="#"><i class="fas fa-user-circle"></i></a>
                <a href="beranda.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Selamat Datang, <?= htmlspecialchars($username); ?></h1>
            <p>Menemukan kembali barang hilang, membawa ketenangan, dan mengembalikan harapan.</p>
            <div class="video-container">
                <video autoplay loop muted playsinline class="banner-video">
                    <source src="../images/banner.mp4" type="video/mp4">
                    Video tidak dapat diputar di browser Anda.
                </video>
            </div>
            <div class="hero-buttons">
                <a href="daftar-barang-hilang.php" class="btn btn-primary">Barang Kehilangan</a>
                <a href="daftar-barang-temuan.php" class="btn btn-success">Barang Temuan</a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h3 class="section-title">Kategori</h3>
            <div class="category-grid">
                <div class="category-item">
                    <a href="kategori-barang.php?kategori=Aksesoris">
                        <i class="fas fa-ring category-icon"></i>
                        <p class="category-name">Aksesoris</p>
                    </a>
                </div>
                <div class="category-item">
                    <a href="kategori-barang.php?kategori=Elektronik">
                        <i class="fas fa-tv category-icon"></i>
                        <p class="category-name">Elektronik</p>
                    </a>
                </div>
                <div class="category-item">
                    <a href="kategori-barang.php?kategori=Dompet/Tas">
                        <i class="fas fa-briefcase category-icon"></i>
                        <p class="category-name">Dompet/Tas</p>
                    </a>
                </div>
                <div class="category-item">
                    <a href="kategori-barang.php?kategori=Dokumen">
                        <i class="fas fa-file-alt category-icon"></i>
                        <p class="category-name">Dokumen</p>
                    </a>
                </div>
                <div class="category-item">
                    <a href="kategori-barang.php?kategori=Lain-lain">
                        <i class="fas fa-ellipsis-h category-icon"></i>
                        <p class="category-name">Lain-lain</p>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- New Items Section -->
    <section class="new-items">
        <div class="container">
            <h3>Penemuan Barang Hilang Terbaru</h3>
            <div class="item-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="item">
                        <img src="../images/<?= $row['foto_barang']; ?>" alt="<?= $row['nama_barang']; ?>">
                        <p><?= $row['nama_barang']; ?> - <?= $row['kategori']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="view-all">
                <a href="daftar-barang-temuan.php" class="btn btn-primary">Lihat Semua</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Lofocam - Lost and Found Campus</p>
            <p>Menemukan kembali barang hilang, membawa ketenangan, dan mengembalikan harapan.</p>
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
