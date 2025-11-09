<?php
$class_id = $_GET['class'] ?? '';
$success_message = '';
$error_message = '';

// Xử lý gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_grades'])) {
        try {
            $pdo->beginTransaction();
            
            foreach ($_POST['students'] as $student_id => $grades) {
                if (!empty($grades['diem_chuyen_can']) || !empty($grades['diem_giua_ky']) || !empty($grades['diem_giua_ky_2'] ?? '') || !empty($grades['diem_thao_luan']) || !empty($grades['diem_cuoi_ky'])) {
                    // Kiểm tra xem bản ghi điểm đã tồn tại chưa
                    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM Diem WHERE MaLopHocPhan = ? AND MaSinhVien = ?");
                    $check_stmt->execute([$class_id, $student_id]);
                    
                    if ($check_stmt->fetchColumn() > 0) {
                        // Cập nhật bản ghi đã tồn tại
                        $stmt = $pdo->prepare("UPDATE Diem 
                                               SET DiemChuyenCan = ?, DiemGiuaKy = ?, DiemGiuaKy2 = ?, DiemThaoLuan = ?, DiemCuoiKy = ?
                                               WHERE MaLopHocPhan = ? AND MaSinhVien = ?");
                        $stmt->execute([
                            $grades['diem_chuyen_can'] ?: null,
                            $grades['diem_giua_ky'] ?: null,
                            ($grades['diem_giua_ky_2'] ?? '') ?: null,
                            $grades['diem_thao_luan'] ?: null,
                            $grades['diem_cuoi_ky'] ?: null,
                            $class_id,
                            $student_id
                        ]);
                    } else {
                        // Thêm bản ghi mới
                        $stmt = $pdo->prepare("INSERT INTO Diem (MaLopHocPhan, MaSinhVien, DiemChuyenCan, DiemGiuaKy, DiemGiuaKy2, DiemThaoLuan, DiemCuoiKy) 
                                               VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $class_id,
                            $student_id,
                            $grades['diem_chuyen_can'] ?: null,
                            $grades['diem_giua_ky'] ?: null,
                            ($grades['diem_giua_ky_2'] ?? '') ?: null,
                            $grades['diem_thao_luan'] ?: null,
                            $grades['diem_cuoi_ky'] ?: null
                        ]);
                    }
                }
            }
            
            $pdo->commit();
            $success_message = "Lưu điểm thành công!";
        } catch (PDOException $e) {
            $pdo->rollback();
            $error_message = "Lỗi khi lưu điểm: " . $e->getMessage();
        }
    }
}

// Lấy thông tin lớp học phần và kiểm tra tồn tại
$class_info = null;
if ($class_id) {
    try {
        $stmt = $pdo->prepare("SELECT *, SoTinChi FROM LopHocPhan WHERE MaLopHocPhan = ? AND TrangThaiLop = 'hoạt động'");
        $stmt->execute([$class_id]);
        $class_info = $stmt->fetch();
        
        // Thông báo nếu không tìm thấy lớp hoặc lớp không hoạt động
        if (!$class_info) {
            $error_message = "Không tìm thấy lớp học phần hoặc lớp đã ngừng hoạt động!";
        }
    } catch (PDOException $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}

// Lấy tham số tìm kiếm
$search_student = isset($_GET['search_student']) ? trim($_GET['search_student']) : '';

// Lấy danh sách sinh viên và điểm số
$students = [];
if ($class_id && $class_info) {
    try {
        $sql = "
            SELECT sv.MaSinhVien, sv.HoTen, sv.Email, 
                   d.DiemChuyenCan, d.DiemGiuaKy, d.DiemGiuaKy2, d.DiemThaoLuan, d.DiemCuoiKy, d.DiemTongKet, d.XepLoaiChu, d.TinhTrang
            FROM SinhVien sv
            JOIN SinhVien_LopHocPhan slhp ON sv.MaSinhVien = slhp.MaSinhVien
            LEFT JOIN Diem d ON sv.MaSinhVien = d.MaSinhVien AND d.MaLopHocPhan = ?
            WHERE slhp.MaLopHocPhan = ? AND slhp.TrangThaiDangKy = 'đang học'";
        
        $params = [$class_id, $class_id];
        
        // Thêm điều kiện tìm kiếm nếu có từ khóa tìm kiếm
        if (!empty($search_student)) {
            $sql .= " AND (sv.MaSinhVien LIKE ? OR sv.HoTen LIKE ?)";
            $search_param = "%{$search_student}%";
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        $sql .= " ORDER BY sv.MaSinhVien";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();
        
        // Thông báo nếu lớp không có sinh viên (chỉ khi không tìm kiếm)
        if (empty($students) && empty($search_student)) {
            echo '<script>showInfo("Lớp học phần này chưa có sinh viên nào. Vui lòng kiểm tra danh sách đăng ký.");</script>';
        }
    } catch (PDOException $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}

// Lấy tất cả lớp học phần để lựa chọn
try {
    $stmt = $pdo->query("SELECT MaLopHocPhan, TenMonHoc FROM LopHocPhan WHERE TrangThaiLop = 'hoạt động' ORDER BY MaLopHocPhan");
    $all_classes = $stmt->fetchAll();
} catch (PDOException $e) {
    $all_classes = [];
}
?>

<h1>Nhập điểm sinh viên</h1>

<?php if ($success_message): ?>
    <script>showSuccess('<?= addslashes($success_message) ?>');</script>
<?php endif; ?>

<?php if ($error_message): ?>
    <script>showError('<?= addslashes($error_message) ?>');</script>
<?php endif; ?>

<!-- Chọn lớp học phần -->
<div class="form-group" style="margin-bottom: 30px;">
    <label class="form-label">Chọn lớp học phần:</label>
    <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: end;">
        <input type="hidden" name="page" value="nhap_diem">
        <div>
            <select name="class" class="form-control" style="min-width: 300px;">
                <option value="">Chọn lớp học phần</option>
                <?php foreach ($all_classes as $cls): ?>
                    <option value="<?= htmlspecialchars($cls['MaLopHocPhan']) ?>" 
                            <?= $cls['MaLopHocPhan'] === $class_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cls['MaLopHocPhan']) ?> - <?= htmlspecialchars($cls['TenMonHoc']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Chọn</button>
    </form>
</div>

<?php if ($class_info): ?>
    <!-- Thông tin lớp học phần -->
    <div style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 4px;">
        <h3 style="margin: 0 0 15px 0; color: #333;">Thông tin lớp học phần</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 10px; font-size: 14px;">
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Mã lớp: </span>
                <span><?= htmlspecialchars($class_info['MaLopHocPhan']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Tên môn: </span>
                <span><?= htmlspecialchars($class_info['TenMonHoc']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Số tín chỉ: </span>
                <span><?= htmlspecialchars($class_info['SoTinChi']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Học kỳ: </span>
                <span><?= htmlspecialchars($class_info['HocKy']) ?> - <?= htmlspecialchars($class_info['NamHoc']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Giảng viên: </span>
                <span><?= htmlspecialchars($class_info['GiangVienPhuTrach']) ?></span>
            </div>
            <div style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <span style="font-weight: bold; color: #555;">Số sinh viên: </span>
                <span style="color: #007bff; font-weight: bold;"><?= count($students) ?></span>
                <?php if (!empty($search_student)): ?>
                    <span style="color: #666; font-size: 12px; margin-left: 10px;">(Đang lọc: "<?= htmlspecialchars($search_student) ?>")</span>
                <?php endif; ?>
                
                <?php 
                // Thống kê và thông báo tiến độ hoàn thành điểm
                if (!empty($students)):
                    $completed_students = array_filter($students, function($s) { 
                        return !is_null($s['DiemTongKet']); 
                    });
                    $completion_rate = count($students) > 0 ? round((count($completed_students) / count($students)) * 100, 1) : 0;
                    
                    if (count($completed_students) > 0):
                        echo '<script>showSuccess("Có ' . count($completed_students) . '/' . count($students) . ' sinh viên đã hoàn thành điểm (' . $completion_rate . '%)");</script>';
                    endif;
                endif;
                ?>
            </div>
        </div>
    </div>

    <!-- Tìm kiếm sinh viên -->
    <div style="background: #fff; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 4px;">
        <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
            <input type="hidden" name="page" value="nhap_diem">
            <input type="hidden" name="class" value="<?= htmlspecialchars($class_id) ?>">
            <label style="font-weight: bold; color: #555; white-space: nowrap;">Tìm sinh viên:</label>
            <input type="text" name="search_student" 
                   value="<?= htmlspecialchars($search_student) ?>" 
                   placeholder="Nhập mã sinh viên hoặc tên sinh viên" 
                   style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: 'Times New Roman', Tahoma, Geneva, Verdana, sans-serif;">
            <button type="submit" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-family: 'Times New Roman', Tahoma, Geneva, Verdana, sans-serif;">Tìm</button>
            <?php if (!empty($search_student)): ?>
                <a href="index.php?page=nhap_diem&class=<?= urlencode($class_id) ?>" 
                   style="padding: 8px 16px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-family: 'Times New Roman', Tahoma, Geneva, Verdana, sans-serif;">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <?php 
    // Thông báo kết quả tìm kiếm sinh viên
    if (isset($_GET['search_student'])):
        if (!empty($search_student)):
            if (count($students) > 0):
                echo '<script>showInfo("Tìm thấy ' . count($students) . ' sinh viên với từ khóa \"' . addslashes($search_student) . '\"");</script>';
            else:
                echo '<script>showWarning("Không tìm thấy sinh viên nào với từ khóa \"' . addslashes($search_student) . '\"");</script>';
            endif;
        else:
            // Thông báo khi ấn tìm mà chưa nhập từ khóa
            echo '<script>showWarning("Vui lòng nhập mã sinh viên hoặc tên sinh viên trước khi tìm kiếm");</script>';
        endif;
    endif;
    ?>

    <?php if (!empty($students)): ?>
        <!-- Form nhập điểm -->
        <form method="POST" action="index.php?page=nhap_diem&class=<?= urlencode($class_id) ?>&search_student=<?= urlencode($search_student) ?>">
            <div class="table-container">
                <table class="table" style="min-width: auto; width: 100%;">
                <thead>
                    <tr>
                        <th>Mã SV</th>
                        <th>Họ tên</th>
                        <th>Chuyên cần<br><small>(0-10)</small></th>
                        <th>Giữa kỳ 1<br><small>(0-10)</small></th>
                        <?php if ($class_info['SoTinChi'] > 2): ?>
                        <th>Giữa kỳ 2<br><small>(0-10)</small></th>
                        <?php endif; ?>
                        <th>Thảo luận<br><small>(0-10)</small></th>
                        <th>Cuối kỳ<br><small>(0-10)</small></th>
                        <th>Tổng kết</th>
                        <th>Xếp loại</th>
                        <th>Tình trạng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['MaSinhVien']) ?></td>
                            <td><?= htmlspecialchars($student['HoTen']) ?></td>
                            <td>
                                <input type="number" 
                                       name="students[<?= $student['MaSinhVien'] ?>][diem_chuyen_can]"
                                       class="form-control" 
                                       min="0" max="10" step="0.1"
                                       value="<?= $student['DiemChuyenCan'] ?? '' ?>"
                                       style="width: 80px; font-size: 12px;">
                            </td>
                            <td>
                                <input type="number" 
                                       name="students[<?= $student['MaSinhVien'] ?>][diem_giua_ky]"
                                       class="form-control" 
                                       min="0" max="10" step="0.1"
                                       value="<?= $student['DiemGiuaKy'] ?? '' ?>"
                                       style="width: 80px; font-size: 12px;">
                            </td>
                            <?php if ($class_info['SoTinChi'] > 2): ?>
                            <td>
                                <input type="number" 
                                       name="students[<?= $student['MaSinhVien'] ?>][diem_giua_ky_2]"
                                       class="form-control" 
                                       min="0" max="10" step="0.1"
                                       value="<?= $student['DiemGiuaKy2'] ?? '' ?>"
                                       style="width: 80px; font-size: 12px;">
                            </td>
                            <?php endif; ?>
                            <td>
                                <input type="number" 
                                       name="students[<?= $student['MaSinhVien'] ?>][diem_thao_luan]"
                                       class="form-control" 
                                       min="0" max="10" step="0.1"
                                       value="<?= $student['DiemThaoLuan'] ?? '' ?>"
                                       style="width: 80px; font-size: 12px;">
                            </td>
                            <td>
                                <input type="number" 
                                       name="students[<?= $student['MaSinhVien'] ?>][diem_cuoi_ky]"
                                       class="form-control" 
                                       min="0" max="10" step="0.1"
                                       value="<?= $student['DiemCuoiKy'] ?? '' ?>"
                                       style="width: 80px; font-size: 12px;">
                            </td>
                            <td style="text-align: center; font-weight: bold;">
                                <?= $student['DiemTongKet'] ?? '-' ?>
                            </td>
                            <td style="text-align: center; font-weight: bold;">
                                <?= $student['XepLoaiChu'] ?? '-' ?>
                            </td>
                            <td style="text-align: center;">
                                <span style="color: <?= ($student['TinhTrang'] === 'Đạt') ? 'green' : (($student['TinhTrang'] === 'Không đạt') ? 'red' : 'gray') ?>; font-weight: bold;">
                                    <?= $student['TinhTrang'] ?? '-' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px; text-align: center;">
                <button type="submit" name="save_grades" class="btn btn-primary">Lưu điểm</button>
                <a href="index.php?page=thong_ke&class=<?= urlencode($class_id) ?>" class="btn btn-success">Xem thống kê</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert" style="background-color: #fff3cd; color: #856404; border-color: #ffeaa7;">
            <?php if (!empty($search_student)): ?>
                Không tìm thấy sinh viên nào với từ khóa "<?= htmlspecialchars($search_student) ?>".
                <a href="index.php?page=nhap_diem&class=<?= urlencode($class_id) ?>" style="color: #856404;">Xem tất cả sinh viên</a>
            <?php else: ?>
                Lớp học phần chưa có sinh viên nào.
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <?php if (empty($class_id)): ?>
        <script>showInfo('Vui lòng chọn lớp học phần để bắt đầu nhập điểm');</script>
    <?php endif; ?>
    <div class="alert" style="background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb;">
        Vui lòng chọn lớp học phần để nhập điểm.
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động gửi form tìm kiếm khi nhấn Enter
    const searchInput = document.querySelector('input[name="search_student"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                showInfo('Đang tìm kiếm...', 1000);
                this.form.submit();
            }
        });
    }
    
    // Validation điểm số real-time (0-10)
    const gradeInputs = document.querySelectorAll('input[type="number"]');
    gradeInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (this.value !== '' && (isNaN(value) || value < 0 || value > 10)) {
                showError('Điểm phải từ 0 đến 10');
                this.focus();
                this.select();
            }
        });
    });
    
    // Loading state khi submit form điểm
    const gradeForm = document.querySelector('form[method="POST"]');
    if (gradeForm) {
        gradeForm.addEventListener('submit', function(e) {
            // Kiểm tra có điểm nào được nhập không
            const inputs = this.querySelectorAll('input[type="number"]');
            let hasData = false;
            inputs.forEach(function(input) {
                if (input.value.trim() !== '') hasData = true;
            });
            
            if (!hasData) {
                showWarning('Vui lòng nhập ít nhất một điểm trước khi lưu!');
                e.preventDefault();
                return false;
            }
            
            // Hiển thị loading
            showInfo('Đang lưu điểm...', 2000);
        });
    }
    
    // Loading cho form chọn lớp
    const classForm = document.querySelector('form[action="index.php"]');
    if (classForm) {
        classForm.addEventListener('submit', function() {
            showInfo('Đang tải thông tin lớp...', 1500);
        });
    }
});
</script>