<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../config/db.php';

// Hitung jumlah data ringkasan
$user_count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$barang_count = $conn->query("SELECT COUNT(*) as total FROM barang")->fetch_assoc()['total'];
$klaim_count = $conn->query("SELECT COUNT(*) as total FROM klaim")->fetch_assoc()['total'];

$username = $_SESSION['username']; // Ambil nama pengguna dari session
?>

<!DOCTYPE HTML>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Lofocam</title>
    <meta name="description" content="Halaman admin untuk mengelola laporan barang hilang dan ditemukan di Lofocam.">
    <meta name="keywords" content="Admin Dashboard, Lost and Found, Lofocam">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="container">
            <div class="logo-section">
                <h1 class="logo">Lofocam Admin</h1>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="home-admin.php" class="active">Dashboard</a></li>
                    <li><a href="kelola-users.php">Kelola User</a></li>
                    <li><a href="kelola-barang.php">Kelola Barang</a></li>
                    <li><a href="kelola-klaim.php">Verifikasi Klaim</a></li>
                </ul>
                <div class="icons">
                    <a href="#"><i class="fas fa-bell"></i></a>
                    <a href="#"><i class="fas fa-user-circle"></i></a>
                    <a href="../php/beranda.php" class="btn btn-logout">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Selamat Datang, Admin <?= htmlspecialchars($username); ?>!</h1>
            <p>Kelola laporan barang hilang, ditemukan, dan klaim verifikasi dengan mudah.</p>
        </div>
    </section>

    <!-- Dashboard Statistics -->
    <section class="dashboard-stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-box">
                    <h3>Total Users</h3>
                    <p><?= $user_count; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Total Barang</h3>
                    <p><?= $barang_count; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Total Klaim</h3>
                    <p><?= $klaim_count; ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h3 class="section-title">Navigasi Cepat</h3>
            <div class="category-grid">
                <div class="category-item">
                    <a href="kelola-users.php">
                        <i class="fas fa-users category-icon"></i>
                        <p class="category-name">Kelola User</p>
                    </a>
                </div>
                <div class="category-item">
                    <a href="kelola-barang.php">
                        <i class="fas fa-box category-icon"></i>
                        <p class="category-name">Kelola Barang</p>
                    </a>
                </div>
                <div class="category-item">
                    <a href="kelola-klaim.php">
                        <i class="fas fa-check-circle category-icon"></i>
                        <p class="category-name">Verifikasi Klaim</p>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Lofocam - Admin Dashboard</p>
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
