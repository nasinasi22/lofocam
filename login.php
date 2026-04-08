<?php
session_start();
require_once '../config/db.php'; // Sambungkan ke file database

$error = ''; // Untuk menyimpan pesan kesalahan

// Cek apakah form disubmit dengan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Query untuk mendapatkan data user berdasarkan username
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error); // Debugging error
        }

        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Periksa apakah user ditemukan dan password sesuai
        if ($user) {
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session untuk user yang berhasil login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect berdasarkan peran (role)
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/home-admin.php");
                    exit;
                } else {
                    header("Location: home.php");
                    exit;
                }
            } else {
                $error = 'Password salah. Silakan coba lagi.';
            }
        } else {
            $error = 'Username tidak ditemukan.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Lofocam</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="container">
            <h1 class="logo">Lofocam</h1>
            <nav>
                <p>Apakah Anda belum punya akun?</p>
                <a href="signup.php" class="btn btn-primary">Daftar</a>
            </nav>
        </div>
    </header>

    <!-- Login Form -->
    <section class="login-form">
        <div class="container">
            <div class="form-container">
                <h2>Masuk</h2>
                <?php if ($error): ?>
                    <p class="error-message" style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan Username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Masuk</button>
                </form>
                <a href="#" class="forgot-password">Lupa Password?</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
