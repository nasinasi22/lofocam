<?php
session_start();
require_once '../config/db.php'; // Sambungkan ke file database

$error = ''; // Untuk menyimpan pesan kesalahan
$success = ''; // Untuk menyimpan pesan sukses

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $full_name = trim($_POST['full_name']);
    $gender = trim($_POST['gender']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    // Validasi password
    if ($password !== $confirm_password) {
        $error = 'Password dan Konfirmasi Password tidak cocok.';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Periksa apakah username atau email sudah terdaftar
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Username atau Email sudah terdaftar.';
        } else {
            // Simpan data ke database
            $insert_query = "INSERT INTO users (username, email, password, full_name, gender, address, phone, role, status) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, 'user', 'Belum Terverifikasi')";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('sssssss', $username, $email, $hashed_password, $full_name, $gender, $address, $phone);

            if ($stmt->execute()) {
                // Redirect ke halaman login setelah berhasil mendaftar
                header("Location: login.php?message=register_success");
                exit;
            } else {
                $error = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Lofocam</title>
    <link rel="stylesheet" href="../css/signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <h1 class="logo">Lofocam</h1>
            <nav>
                <p>Apakah Anda sudah punya akun?</p>
                <a href="login.php" class="btn btn-primary">Masuk</a>
            </nav>
        </div>
    </header>

    <section class="signup-form">
        <div class="container">
            <div class="form-container">
                <h2>Buat Akun</h2>
                <!-- Tampilkan pesan error -->
                <?php if ($error): ?>
                    <p class="error-message"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <form action="signup.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan Username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                    </div>
                    <div class="form-group">
                        <label for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Jenis Kelamin</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <input type="text" id="address" name="address" placeholder="Masukkan Alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Nomor Handphone</label>
                        <input type="tel" id="phone" name="phone" placeholder="Masukkan Nomor Handphone" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Lofocam | Lost & Found</p>
            <p>Menemukan kembali barang hilang, membawa ketenangan, dan mengembalikan harapan.</p>
            <ul class="social-icons">
                <li><a href="#"><i class="fab fa-facebook"></i></a></li>
                <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                <li><a href="#"><i class="fab fa-x-twitter"></i></a></li>
                <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
            </ul>
        </div>
    </footer>
</body>
</html>
