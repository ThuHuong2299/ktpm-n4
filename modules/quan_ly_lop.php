<?php
// Khởi tạo biến ngay từ đầu
$action = $_GET['action'] ?? 'list';
$success_message = '';
$error_message = '';

// Xử lý gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_class'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO LopHocPhan (MaLopHocPhan, TenMonHoc, SoTinChi, HocKy, NamHoc, GiangVienPhuTrach, TrangThaiLop) 
                                  VALUES (:ma_lop, :ten_mon, :so_tin_chi, :hoc_ky, :nam_hoc, :giang_vien, 'hoạt động')");
            $stmt->execute([
                ':ma_lop' => $_POST['ma_lop'],
                ':ten_mon' => $_POST['ten_mon'],
                ':so_tin_chi' => $_POST['so_tin_chi'],
                ':hoc_ky' => $_POST['hoc_ky'],
                ':nam_hoc' => $_POST['nam_hoc'],
                ':giang_vien' => $_POST['giang_vien']
            ]);
            $success_message = "Thêm lớp học phần thành công!";
            $action = 'list';
        } catch (Exception $e) {
            $error_message = "Lỗi: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_class'])) {
        try {
            $stmt = $pdo->prepare("UPDATE LopHocPhan SET TenMonHoc = :ten_mon, SoTinChi = :so_tin_chi, HocKy = :hoc_ky, 
                                  NamHoc = :nam_hoc, GiangVienPhuTrach = :giang_vien WHERE MaLopHocPhan = :ma_lop");
            $stmt->execute([
                ':ma_lop' => $_POST['ma_lop'],
                ':ten_mon' => $_POST['ten_mon'],
                ':so_tin_chi' => $_POST['so_tin_chi'],
                ':hoc_ky' => $_POST['hoc_ky'],
                ':nam_hoc' => $_POST['nam_hoc'],
                ':giang_vien' => $_POST['giang_vien']
            ]);
            $success_message = "Cập nhật lớp học phần thành công!";
            $action = 'list';
        } catch (Exception $e) {
            $error_message = "Lỗi: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_class'])) {
        try {
            $stmt = $pdo->prepare("UPDATE LopHocPhan SET TrangThaiLop = 'đã xóa' WHERE MaLopHocPhan = :id");
            $stmt->execute([':id' => $_POST['class_id']]);
            $success_message = "Xóa lớp học phần thành công!";
        } catch (Exception $e) {
            $error_message = "Lỗi: " . $e->getMessage();
        }
    }
}

// Lấy thông tin lớp để edit nếu cần
$edit_class = null;
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM LopHocPhan WHERE MaLopHocPhan = :id AND TrangThaiLop = 'hoạt động'");
        $stmt->execute([':id' => $_GET['id']]);
        $edit_class = $stmt->fetch();
        if (!$edit_class) {
            $error_message = "Không tìm thấy lớp học phần!";
            $action = 'list';
        }
    } catch (Exception $e) {
        $error_message = "Lỗi: " . $e->getMessage();
        $action = 'list';
    }
}

// Lấy danh sách lớp với tìm kiếm
try {
    $search_query = '';
    $where_conditions = ["TrangThaiLop = 'hoạt động'"];
    $params = [];

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_query = trim($_GET['search']);
        $where_conditions[] = "(MaLopHocPhan LIKE :search OR TenMonHoc LIKE :search OR GiangVienPhuTrach LIKE :search)";
        $params[':search'] = "%{$search_query}%";
    }

    $where_clause = implode(' AND ', $where_conditions);
    $sql = "SELECT * FROM LopHocPhan WHERE {$where_clause} ORDER BY MaLopHocPhan";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $classes = $stmt->fetchAll();
} catch (Exception $e) {
    $error_message = "Lỗi: " . $e->getMessage();
    $classes = [];
}
?>

<?php if ($action !== 'edit'): ?>
<h1>Quản lý lớp học phần</h1>
<?php endif; ?>

<?php if ($success_message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<?php if ($action === 'add'): ?>
    <!-- Form thêm lớp học phần -->
    <h2>Thêm lớp học phần mới</h2>
    <form method="POST" action="index.php?page=quan_ly_lop">
        <div class="form-group">
            <label class="form-label">Mã lớp học phần:</label>
            <input type="text" name="ma_lop" class="form-control" required 
                   placeholder="VD: 001IT1234_01" 
                   pattern="^(001|002|003)[A-Z0-9]{6,8}_[0-9]{2}$"
                   title="Mã lớp phải có định dạng: 001/002/003 + 6-8 ký tự chữ/số + _ + 2 số">
        </div>
        <div class="form-group">
            <label class="form-label">Tên môn học:</label>
            <input type="text" name="ten_mon" class="form-control" required maxlength="100">
        </div>
        <div class="form-group">
            <label class="form-label">Số tín chỉ:</label>
            <select name="so_tin_chi" class="form-control" required>
                <option value="">Chọn số tín chỉ</option>
                <option value="1">1 tín chỉ</option>
                <option value="2">2 tín chỉ</option>
                <option value="3">3 tín chỉ</option>
                <option value="4">4 tín chỉ</option>
                <option value="5">5 tín chỉ</option>
                <option value="6">6 tín chỉ</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Học kỳ:</label>
            <select name="hoc_ky" class="form-control" required>
                <option value="">Chọn học kỳ</option>
                <option value="1">Học kỳ 1</option>
                <option value="2">Học kỳ 2</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Năm học:</label>
            <input type="text" name="nam_hoc" class="form-control" required 
                   placeholder="VD: 2023-2024" 
                   pattern="^[0-9]{4}-[0-9]{4}$"
                   title="Năm học phải có định dạng: YYYY-YYYY">
        </div>
        <div class="form-group">
            <label class="form-label">Giảng viên phụ trách:</label>
            <input type="text" name="giang_vien" class="form-control" required maxlength="100">
        </div>
        <div style="margin-top: 20px;">
            <button type="submit" name="add_class" class="btn btn-primary">Thêm lớp</button>
            <a href="index.php?page=quan_ly_lop" class="btn">Hủy</a>
        </div>
    </form>

<?php elseif ($action === 'edit' && $edit_class): ?>
    <!-- Form cập nhật thông tin lớp học phần -->
    <?php
    // Khởi tạo phiên làm việc nếu chưa có
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Danh sách lý do gợi ý
    $ly_do_goi_y = [
        "Lý do sức khỏe",
        "Bận công tác xa", 
        "Xung đột lịch giảng dạy",
        "Lý do gia đình",
        "Chuyển công tác",
        "Nghỉ phép dài hạn",
        "Khác"
    ];

    $thong_bao_update = '';
    $loi_update = '';

    // Xử lý khi biểu mẫu được gửi
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cap_nhat_giang_day'])) {
        $ma_lop = $edit_class['MaLopHocPhan'];
        $ly_do_chon = $_POST['ly_do_chon'] ?? '';
        $ly_do_chi_tiet = trim($_POST['ly_do_chi_tiet'] ?? '');
        $giang_vien_thay_the = trim($_POST['giang_vien_thay_the'] ?? '');
        
        // Kiểm tra dữ liệu đầu vào cơ bản
        if (empty($ly_do_chon)) {
            $loi_update = "Vui lòng chọn lý do!";
        } elseif ($ly_do_chon == 'Khác' && empty($ly_do_chi_tiet)) {
            $loi_update = "Vui lòng nhập lý do chi tiết!";
        } else {
            // Lưu đề xuất vào phiên làm việc
            if (!isset($_SESSION['de_xuat'])) {
                $_SESSION['de_xuat'] = [];
            }
            
            $de_xuat_id = 'DX_' . date('YmdHis') . '_' . rand(100, 999);
            $_SESSION['de_xuat'][$de_xuat_id] = [
                'ma_lop' => $ma_lop,
                'ten_mon' => $edit_class['TenMonHoc'],
                'giang_vien' => $edit_class['GiangVienPhuTrach'],
                'ly_do_chon' => $ly_do_chon,
                'ly_do_chi_tiet' => $ly_do_chi_tiet,
                'giang_vien_thay_the' => $giang_vien_thay_the,
                'ngay_gui' => date('Y-m-d H:i:s'),
                'trang_thai' => 'Chờ duyệt'
            ];
            
            $thong_bao_update = "Đã gửi đề xuất thay đổi giảng viên cho lớp {$ma_lop} thành công!";
        }
    }

    // Lấy danh sách các đề xuất đã gửi cho lớp học phần này
    $ds_de_xuat_lop = [];
    if (isset($_SESSION['de_xuat'])) {
        foreach ($_SESSION['de_xuat'] as $id => $de_xuat) {
            if ($de_xuat['ma_lop'] == $edit_class['MaLopHocPhan']) {
                $ds_de_xuat_lop[$id] = $de_xuat;
            }
        }
    }
    ?>
    
    <h1>Cập nhật thông tin lớp</h1>
    
    <!-- Khu vực hiển thị thông báo -->
    <?php if ($thong_bao_update): ?>
        <div class="alert alert-success"><?php echo $thong_bao_update; ?></div>
    <?php endif; ?>
    
    <?php if ($loi_update): ?>
        <div class="alert alert-error"><?php echo $loi_update; ?></div>
    <?php endif; ?>

    <!-- Khung thông tin lớp học phần -->
    <div style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 4px; font-family: 'Times New Roman', Tahoma, Geneva, Verdana, sans-serif;">
        <h3 style="margin: 0 0 15px 0; color: #333; font-family: 'Times New Roman', Tahoma, Geneva, Verdana, sans-serif;">Thông tin lớp học phần</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 10px; font-size: 14px; font-family: 'Times New Roman', Tahoma, Geneva, Verdana, sans-serif;">
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Mã lớp: </span>
                <span><?= htmlspecialchars($edit_class['MaLopHocPhan']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Tên môn: </span>
                <span><?= htmlspecialchars($edit_class['TenMonHoc']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Số tín chỉ: </span>
                <span><?= htmlspecialchars($edit_class['SoTinChi']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Học kỳ: </span>
                <span><?= htmlspecialchars($edit_class['HocKy']) ?> - <?= htmlspecialchars($edit_class['NamHoc']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Giảng viên: </span>
                <span><?= htmlspecialchars($edit_class['GiangVienPhuTrach']) ?></span>
            </div>
        </div>
    </div>

    <!-- Biểu mẫu cập nhật giảng dạy -->
    <div class="form-container">
        <h3>Cập nhật giảng dạy</h3>
        <p>Gửi đề xuất thay đổi giảng viên phụ trách cho lớp học phần này</p>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="ly_do_chon">Lý do không thể giảng dạy:</label>
                <select id="ly_do_chon" name="ly_do_chon" required onchange="toggleChiTiet()">
                    <option value="">Chọn lý do</option>
                    <?php foreach ($ly_do_goi_y as $ly_do): ?>
                        <option value="<?php echo $ly_do; ?>">
                            <?php echo $ly_do; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" id="chi-tiet-group" style="display: none;">
                <label for="ly_do_chi_tiet">Lý do chi tiết:</label>
                <textarea id="ly_do_chi_tiet" name="ly_do_chi_tiet" rows="3" 
                          placeholder="Nhập lý do chi tiết..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="giang_vien_thay_the">Đề xuất giảng viên thay thế (tùy chọn):</label>
                <input type="text" id="giang_vien_thay_the" name="giang_vien_thay_the" 
                       placeholder="Ví dụ: TS. Nguyễn Văn B">
                <small style="color: #666; font-size: 12px;">Để trống nếu không có đề xuất cụ thể</small>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" name="cap_nhat_giang_day" class="btn btn-primary">Gửi đề xuất</button>
                <a href="index.php?page=quan_ly_lop" class="btn">Quay lại</a>
            </div>
        </form>
    </div>

    <!-- Bảng danh sách đề xuất đã gửi cho lớp học phần này -->
    <?php if (!empty($ds_de_xuat_lop)): ?>
    <div class="form-container">
        <h3>Đề xuất đã gửi cho lớp này</h3>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã ĐX</th>
                        <th>Lý do</th>
                        <th>GV đề xuất</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ds_de_xuat_lop as $id => $de_xuat): ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo htmlspecialchars($de_xuat['ly_do_chon']); ?></td>
                        <td><?php echo !empty($de_xuat['giang_vien_thay_the']) ? htmlspecialchars($de_xuat['giang_vien_thay_the']) : '<i style="color: #999;">Chưa có</i>'; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($de_xuat['ngay_gui'])); ?></td>
                        <td><span class="badge badge-warning"><?php echo $de_xuat['trang_thai']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <script>
    function toggleChiTiet() {
        const select = document.getElementById('ly_do_chon');
        const chiTietGroup = document.getElementById('chi-tiet-group');
        
        if (select.value === 'Khác') {
            chiTietGroup.style.display = 'block';
            document.getElementById('ly_do_chi_tiet').required = true;
        } else {
            chiTietGroup.style.display = 'none';
            document.getElementById('ly_do_chi_tiet').required = false;
        }
    }

    // Kiểm tra khi tải trang
    document.addEventListener('DOMContentLoaded', function() {
        toggleChiTiet();
    });
    </script>

<?php elseif ($action === 'delete' && isset($_GET['id'])): ?>
    <!-- Xác nhận xóa lớp học phần -->
    <h2>Xác nhận xóa lớp học phần</h2>
    <?php
    try {
        $stmt = $pdo->prepare("SELECT * FROM LopHocPhan WHERE MaLopHocPhan = :id AND TrangThaiLop = 'hoạt động'");
        $stmt->execute([':id' => $_GET['id']]);
        $delete_class = $stmt->fetch();
        
        if ($delete_class):
    ?>
        <div style="background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; margin-bottom: 20px;">
            <p><strong>Bạn có chắc chắn muốn xóa lớp học phần này?</strong></p>
            <p><strong>Mã lớp:</strong> <?= htmlspecialchars($delete_class['MaLopHocPhan']) ?></p>
            <p><strong>Tên môn:</strong> <?= htmlspecialchars($delete_class['TenMonHoc']) ?></p>
            <p><strong>Giảng viên:</strong> <?= htmlspecialchars($delete_class['GiangVienPhuTrach']) ?></p>
        </div>
        
        <form method="POST" action="index.php?page=quan_ly_lop">
            <input type="hidden" name="ma_lop" value="<?= htmlspecialchars($delete_class['MaLopHocPhan']) ?>">
            <div class="form-group">
                <label class="form-label">Lý do xóa:</label>
                <textarea name="ly_do" class="form-control" rows="3" placeholder="Nhập lý do xóa lớp học phần..."></textarea>
            </div>
            <div style="margin-top: 20px;">
                <button type="submit" name="delete_class" class="btn btn-danger" 
                        onclick="return confirm('Bạn có chắc chắn muốn xóa lớp này?')">Xác nhận xóa</button>
                <a href="index.php?page=quan_ly_lop" class="btn">Hủy</a>
            </div>
        </form>
    <?php 
        else:
            echo '<div class="alert alert-error">Không tìm thấy lớp học phần!</div>';
        endif;
    } catch (PDOException $e) {
        echo '<div class="alert alert-error">Lỗi: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>

<?php else: ?>
    <!-- Danh sách lớp học phần -->
    <div style="margin-bottom: 20px;">
        <a href="index.php?page=quan_ly_lop&action=add" class="btn btn-primary">Thêm lớp học phần</a>
    </div>
    
    <!-- Form tìm kiếm -->
    <form class="search-form" method="GET" action="">
        <input type="hidden" name="page" value="quan_ly_lop">
        <input type="text" name="search" id="searchInput" placeholder="Nhập mã lớp hoặc tên môn" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Tra cứu</button>
    </form>
    
    <!-- Bảng dữ liệu -->
    <div class="table-container">
        <table id="gradeTable">
            <thead>
                <tr>
                    <th>Mã lớp</th>
                    <th>Tên môn học</th>
                    <th>Số tín chỉ</th>
                    <th>Học kỳ</th>
                    <th>Năm học</th>
                    <th>Giảng viên</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?= htmlspecialchars($class['MaLopHocPhan']) ?></td>
                            <td><?= htmlspecialchars($class['TenMonHoc']) ?></td>
                            <td><?= htmlspecialchars($class['SoTinChi']) ?></td>
                            <td><?= htmlspecialchars($class['HocKy']) ?></td>
                            <td><?= htmlspecialchars($class['NamHoc']) ?></td>
                            <td><?= htmlspecialchars($class['GiangVienPhuTrach']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="index.php?page=thong_ke&class=<?= urlencode($class['MaLopHocPhan']) ?>" 
                                       class="btn btn-view">Xem</a>
                                    <a href="index.php?page=quan_ly_lop&action=edit&id=<?= urlencode($class['MaLopHocPhan']) ?>" 
                                       class="btn btn-edit">Cập nhật</a>
                                    <a href="index.php?page=nhap_diem&class=<?= urlencode($class['MaLopHocPhan']) ?>" 
                                       class="btn btn-delete">Nhập điểm</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="empty-message">Chưa có lớp học phần nào trong hệ thống. <a href="index.php?page=quan_ly_lop&action=add" style="text-decoration: none;">Thêm lớp học phần đầu tiên</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>