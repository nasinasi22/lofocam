<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Kosongkan jika tanpa password
$dbname = 'lofocam';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
