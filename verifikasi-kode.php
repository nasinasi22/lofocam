<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode'];
    $email = $_SESSION['verifikasi_email'];

    $sql = "SELECT * FROM verifikasi WHERE email = ? AND kode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $email, $kode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sqlUpdate = "UPDATE users SET status = 'Terverifikasi' WHERE email = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param('s', $email);
        $stmtUpdate->execute();

        unset($_SESSION['verifikasi_email']);
        header("Location: login.php");
        exit();
    } else {
        echo "Kode verifikasi salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode</title>
    <link rel="stylesheet" href="../CSS/verifikasi-kode.css">
</head>
<body>
    <h1>Verifikasi Email</h1>
    <form method="POST">
        <input type="text" name="kode" placeholder="Masukkan kode verifikasi" required>
        <button type="submit">Verifikasi</button>
    </form>
</body>
</html>
