-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS lofocam;
USE lofocam;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('Laki-laki', 'Perempuan') NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(15) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('Terverifikasi', 'Belum Terverifikasi') DEFAULT 'Belum Terverifikasi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Barang
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    no_hp VARCHAR(15) NOT NULL,
    foto_barang VARCHAR(255),
    fakultas ENUM('FTI', 'FTSP', 'FK', 'FIAI', 'FEB', 'FPSB') NOT NULL,
    kategori ENUM('Aksesoris', 'Elektronik', 'Dompet/Tas', 'Dokumen', 'Lain-lain') NOT NULL,
    tanggal_hilang DATE NULL,
    tanggal_temuan DATE NULL,
    status ENUM('Belum Ditemukan', 'Ditemukan') DEFAULT 'Belum Ditemukan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Klaim
CREATE TABLE IF NOT EXISTS klaim (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barang_id INT NOT NULL,
    user_id INT NOT NULL,
    deskripsi TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Verifikasi
CREATE TABLE IF NOT EXISTS verifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    kode VARCHAR(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Reset Password
CREATE TABLE IF NOT EXISTS reset_password (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    kode VARCHAR(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data Awal (Seed Data)
INSERT INTO users (username, email, password, full_name, gender, address, phone, role, status)
VALUES 
('admin', 'admin@lofocam.com', '$2y$10$9gGfhO7xl8mWAhW3LYNhyu28lx3fS8mLk.eOHqYAVChU7VgPaPklq', 'Administrator', 'Laki-laki', 'Jalan Admin No. 1', '081234567890', 'admin', 'Terverifikasi')
ON DUPLICATE KEY UPDATE username = VALUES(username);

INSERT INTO barang (nama_barang, deskripsi, no_hp, fakultas, kategori, tanggal_hilang, status)
VALUES
('Laptop Asus', 'Hilang di ruang kelas FTI', '081234567890', 'FTI', 'Elektronik', '2024-12-10', 'Belum Ditemukan'),
('Dompet Biru', 'Hilang di kantin FEB', '081234567891', 'FEB', 'Dompet/Tas', '2024-12-09', 'Belum Ditemukan'),
('Charger HP', 'Ditemukan di perpustakaan FK', '081234567892', 'FK', 'Elektronik', NULL, 'Ditemukan'),
('Ijazah Sarjana', 'Hilang di laboratorium FTI', '081234567893', 'FTI', 'Dokumen', '2024-12-07', 'Belum Ditemukan'),
('Gelang Emas', 'Ditemukan di area parkir FEB', '081234567894', 'FEB', 'Aksesoris', NULL, 'Ditemukan');
