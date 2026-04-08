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

// Query dasar untuk barang hilang
$sql = "SELECT id, nama_barang, deskripsi, no_hp, fakultas, kategori, foto_barang, tanggal_hilang 
        FROM barang 
        WHERE status = 'Belum Ditemukan'";

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
    <title>Daftar Barang Hilang</title>
    <link rel="stylesheet" href="../css/daftar-barang-hilang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">Lofocam</div> <!-- Logo di kiri -->
        <div class="nav-container"> <!-- Navbar Container -->
            <ul class="nav-links">
                <li><a href="home.php">Beranda</a></li>
                <li><a href="daftar-barang-hilang.php" class="active">Daftar Barang Hilang</a></li>
                <li><a href="daftar-barang-temuan.php">Daftar Barang Temuan</a></li>
                <li><a href="lapor.php">Lapor</a></li>
            </ul>
            <div class="icons"> <!-- Bagian ikon dan tombol logout -->
                <a href="#"><i class="fas fa-bell"></i></a>
                <a href="#"><i class="fas fa-user-circle"></i></a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <aside class="filter-section">
            <form method="GET" action="daftar-barang-hilang.php">
                <input type="text" name="search" placeholder="Cari Barang..." value="<?= htmlspecialchars($search) ?>">
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
                <button type="submit" class="btn">Cari</button>
            </form>
        </aside>

        <!-- Result Section -->
        <section class="result-section">
            <h1>Daftar Barang Hilang</h1>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama Barang</th>
                            <th>Deskripsi</th>
                            <th>Nomor HP</th>
                            <th>Fakultas</th>
                            <th>Kategori</th>
                            <th>Tanggal Hilang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../uploads/<?= htmlspecialchars($row['foto_barang']) ?>" alt="Foto Barang" class="item-photo">
                                </td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                <td><?= htmlspecialchars($row['fakultas']) ?></td>
                                <td><?= htmlspecialchars($row['kategori']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_hilang']) ?></td>
                                <td>
                                    <a href="klaim-barang-hilang.php?id=<?= $row['id'] ?>" class="btn btn-primary">Klaim</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
