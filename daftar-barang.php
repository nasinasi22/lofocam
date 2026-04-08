<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

// Filter data
$fakultas = isset($_GET['fakultas']) ? $_GET['fakultas'] : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query dasar untuk barang (mengambil data terbaru)
$sql = "SELECT id, nama_barang, deskripsi, no_hp, fakultas, kategori, foto_barang, tanggal_hilang, tanggal_temuan 
        FROM barang 
        WHERE status IN ('Ditemukan', 'Belum Ditemukan')";

// Tambahkan filter jika ada
$params = [];
if ($fakultas) {
    $sql .= " AND fakultas = ?";
    $params[] = $fakultas;
}
if ($kategori) {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
}
if ($search) {
    $sql .= " AND nama_barang LIKE ?";
    $params[] = "%" . $search . "%";
}

// Urutkan berdasarkan tanggal terbaru
$sql .= " ORDER BY created_at DESC";

// Siapkan query
$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang Hilang dan Temuan</title>
    <link rel="stylesheet" href="../css/daftar-barang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">Lofocam</div>
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="home.php">Beranda</a></li>
                <li><a href="daftar-barang-hilang.php">Daftar Barang Hilang</a></li>
                <li><a href="daftar-barang-temuan.php">Daftar Barang Temuan</a></li>
                <li><a href="lapor.php">Lapor</a></li>
            </ul>
            <div class="icons">
                <a href="#"><i class="fas fa-bell"></i></a>
                <a href="#"><i class="fas fa-user-circle"></i></a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <!-- Search Form Section -->
    <section class="search-section">
        <form method="GET" action="daftar-barang.php">
            <input type="text" name="search" placeholder="Cari Barang..." value="<?= htmlspecialchars($search) ?>" />
            <button type="submit" class="btn-search">Cari</button>
        </form>
    </section>

    <!-- Main Content -->
    <main>
        <aside class="filter-section">
            <form method="GET" action="daftar-barang.php">
                <h3>Fakultas</h3>
                <ul>
                    <?php foreach (['FTI', 'FTSP', 'FK', 'FIAI', 'FEB', 'FPSB'] as $f): ?>
                        <li>
                            <input type="radio" name="fakultas" value="<?= $f ?>" <?= $fakultas == $f ? 'checked' : '' ?>>
                            <?= htmlspecialchars($f) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h3>Kategori</h3>
                <ul>
                    <?php foreach (['Aksesoris', 'Elektronik', 'Dompet/Tas', 'Dokumen', 'Lain-lain'] as $cat): ?>
                        <li>
                            <input type="radio" name="kategori" value="<?= $cat ?>" <?= $kategori == $cat ? 'checked' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit" class="btn">Terapkan Filter</button>
            </form>
        </aside>

        <!-- Result Section -->
        <section class="result-section">
            <h1>Daftar Barang Hilang dan Temuan</h1>
            <div class="card-wrapper">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <div class="card-image">
                            <?php if (!empty($row['foto_barang'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($row['foto_barang']); ?>" alt="Foto Barang">
                            <?php else: ?>
                                <p>Tidak Ada Foto</p>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3><?= htmlspecialchars($row['nama_barang']); ?></h3>
                            <p><strong>Kategori:</strong> <?= htmlspecialchars($row['kategori']); ?></p>
                            <p><strong>Deskripsi:</strong> <?= htmlspecialchars($row['deskripsi']); ?></p>
                            <p><strong>Fakultas:</strong> <?= htmlspecialchars($row['fakultas']); ?></p>
                            <p><strong>Tanggal:</strong> 
                                <?= $row['tanggal_hilang'] ? 'Tanggal Hilang: ' . htmlspecialchars($row['tanggal_hilang']) : ''; ?>
                                <?= $row['tanggal_temuan'] ? 'Tanggal Temuan: ' . htmlspecialchars($row['tanggal_temuan']) : ''; ?>
                            </p>
                        </div>
                        <div class="card-action">
                            <?php
                                if ($row['tanggal_temuan']) {
                                    $klaimUrl = "klaim-barang-temuan.php?id=" . $row['id'];
                                } else {
                                    $klaimUrl = "klaim-barang-hilang.php?id=" . $row['id'];
                                }
                            ?>
                            <a href="<?= $klaimUrl ?>" class="btn-claim">KLAIM</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>
</body>
</html>
