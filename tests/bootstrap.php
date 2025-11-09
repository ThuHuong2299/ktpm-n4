<?php
// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Thiết lập báo cáo lỗi đầy đủ cho môi trường test
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Định nghĩa đường dẫn gốc của dự án (nếu cần sử dụng trong test)
define('ROOT_PATH', dirname(__DIR__));

// Hiển thị thông tin môi trường test
echo "PHPUnit Bootstrap: Môi trường kiểm thử tích hợp đã được khởi tạo!\n";
echo "Root Path: " . ROOT_PATH . "\n";
echo "Timezone: " . date_default_timezone_get() . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "==========================================\n";