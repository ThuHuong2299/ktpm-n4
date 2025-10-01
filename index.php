<?php
// Include database configuration
require_once 'config/database.php';

// Include header
include 'includes/header.php';

// Get the current page from URL parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'trang_chu';

// Validate page parameter to prevent directory traversal
$allowed_pages = ['trang_chu', 'quan_ly_lop', 'nhap_diem', 'thong_ke'];

if (in_array($page, $allowed_pages)) {
    $module_file = "modules/{$page}.php";
    if (file_exists($module_file)) {
        include $module_file;
    } else {
        echo "<h2>Trang không tồn tại</h2>";
    }
} else {
    echo "<h2>Trang không hợp lệ</h2>";
}

// Include footer
include 'includes/footer.php';
?>