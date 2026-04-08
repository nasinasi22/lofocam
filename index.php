<?php
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['user'])) {
    // Jika sudah login, arahkan ke beranda
    header("Location: PHP/beranda.php");
    exit();
} else {
    // Jika belum login, arahkan ke halaman login
    header("Location: PHP/login.php");
    exit();
}
?>
