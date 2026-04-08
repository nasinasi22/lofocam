<?php
session_start();
require_once '../config/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil nomor HP user berdasarkan user_id
$query_no_hp = "SELECT phone FROM users WHERE id = ?";
$stmt = $conn->prepare($query_no_hp);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result_no_hp = $stmt->get_result();
$no_hp = $result_no_hp->fetch_assoc()['phone'];

// Hapus laporan jika tombol delete ditekan
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_query = "DELETE FROM barang WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
}

// Ambil data laporan yang sesuai dengan nomor HP user
$query = "SELECT * FROM barang WHERE no_hp = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $no_hp);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan | Lofocam</title>
    <link rel="stylesheet" href="../css/riwayat-laporan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">Lofocam</div>
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="home.php">Beranda</a></li>
                <li><a href="riwayat-laporan.php" class="active">Riwayat Laporan</a></li>
                <li><a href="lapor.php">Lapor</a></li>
            </ul>
            <div class="icons">
                <a href="#"><i class="fas fa-bell"></i></a>
                <a href="#"><i class="fas fa-user-circle"></i></a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <h1>Riwayat Laporan Anda</h1>
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Barang</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['foto_barang'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($row['foto_barang']); ?>" alt="Foto Barang" width="100" height="100" style="object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    Tidak Ada Foto
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                            <td><?= htmlspecialchars($row['kategori']); ?></td>
                            <td>
                                <?php
                                // Menampilkan tanggal hilang atau tanggal ditemukan tergantung pada kategori
                                if ($row['kategori'] === 'Elektronik' || $row['kategori'] === 'Aksesoris' || $row['kategori'] === 'Dompet/Tas' || $row['kategori'] === 'Dokumen' || $row['kategori'] === 'Lain-lain') {
                                    // Cek kategori dan tampilkan tanggal yang sesuai
                                    if (!empty($row['tanggal_hilang'])) {
                                        echo "Tanggal Hilang: " . htmlspecialchars($row['tanggal_hilang']);
                                    } elseif (!empty($row['tanggal_temuan'])) {
                                        echo "Tanggal Temuan: " . htmlspecialchars($row['tanggal_temuan']);
                                    } else {
                                        echo 'N/A';
                                    }
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['status']); ?></td>
                            <td>
                                <!-- Perbaiki URL di sini -->
                                <a href="edit-laporan.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="delete_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Anda yakin ingin menghapus laporan ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Anda belum memiliki laporan barang yang dilaporkan.</p>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Lofocam - Lost and Found Campus</p>
    </footer>
</body>
</html>
