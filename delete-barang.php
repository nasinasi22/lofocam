<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

$barang_id = intval($_GET['id']);
if ($barang_id) {
    $stmt = $conn->prepare("DELETE FROM barang WHERE id = ?");
    $stmt->bind_param("i", $barang_id);
    $stmt->execute();
}
header("Location: kelola-barang.php?message=deleted");
exit();
?>
