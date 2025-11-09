<?php
// Cấu hình Database
$host = 'localhost';
$dbname = 'QuanLyDiem';
$username = 'root';     // Thay đổi nếu cần
$password = '';         // Thay đổi nếu cần

// Kết nối mysqli
$conn = mysqli_connect($host, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}

// Đặt charset cho kết nối
mysqli_set_charset($conn, "utf8mb4");

// Kết nối PDO (để sử dụng trong tương lai nếu cần)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Không dừng chương trình vì đã có kết nối mysqli
    error_log("PDO Connection failed: " . $e->getMessage());
}
?>