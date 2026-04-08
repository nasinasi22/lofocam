<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../config/db.php';

$username = $email = $role = $status = "";
$errors = [];
$editMode = false;

if (isset($_GET['id'])) {
    $editMode = true;
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM users WHERE id = $id");
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $username = $data['username'];
        $email = $data['email'];
        $role = $data['role'];
        $status = $data['status'];
    } else {
        header("Location: kelola-users.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    if ($editMode) {
        $query = "UPDATE users SET username=?, email=?, role=?, status=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $username, $email, $role, $status, $id);
    } else {
        $password = password_hash('default123', PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $username, $email, $password, $role, $status);
    }

    if ($stmt->execute()) {
        header("Location: kelola-users.php");
        exit();
    } else {
        $errors[] = "Terjadi kesalahan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editMode ? 'Edit' : 'Tambah'; ?> User</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header class="navbar">
        <h1><?= $editMode ? 'Edit' : 'Tambah'; ?> User</h1>
        <a href="kelola-users.php" class="btn btn-secondary">Kembali</a>
    </header>
    <main class="container">
        <form method="POST" class="form-container">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="user" <?= $role === 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="Terverifikasi" <?= $status === 'Terverifikasi' ? 'selected' : ''; ?>>Terverifikasi</option>
                    <option value="Belum Terverifikasi" <?= $status === 'Belum Terverifikasi' ? 'selected' : ''; ?>>Belum Terverifikasi</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?= $editMode ? 'Simpan Perubahan' : 'Tambah User'; ?></button>
        </form>
    </main>
</body>
</html>

