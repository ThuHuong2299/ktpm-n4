<?php
// Đảm bảo biến $page được định nghĩa
if (!isset($page)) {
    $page = isset($_GET['page']) ? $_GET['page'] : 'trang_chu';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý điểm sinh viên</title>
    <link rel="stylesheet" href="/assests/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <ul class="nav-menu">
            <li><a href="index.php?page=trang_chu" <?php echo ($page == 'trang_chu' ? 'class="active"' : ''); ?>>Trang chủ</a></li>
            <li><a href="index.php?page=quan_ly_lop" <?php echo ($page == 'quan_ly_lop' ? 'class="active"' : ''); ?>>Quản lý lớp</a></li>
            <li><a href="index.php?page=nhap_diem" <?php echo ($page == 'nhap_diem' ? 'class="active"' : ''); ?>>Nhập điểm</a></li>
            <li><a href="index.php?page=thong_ke" <?php echo ($page == 'thong_ke' ? 'class="active"' : ''); ?>>Thống kê/Tra cứu</a></li>
        </ul>
    </nav>
    <!-- Main Content -->
    <div class="container">
