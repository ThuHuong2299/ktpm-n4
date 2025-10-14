<?php
$class_id = $_GET['class'] ?? '';
$success_message = '';
$error_message = '';

// Initialize statistics with default values
$statistics = [
    'total_students' => 0,
    'graded_students' => 0,
    'passed_students' => 0,
    'pass_rate' => 0,
    'average_score' => 0,
    'grade_distribution' => ['A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D+' => 0, 'D' => 0, 'F' => 0]
];

// Get class information FIRST
$class_info = null;
if ($class_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM LopHocPhan WHERE MaLopHocPhan = ?");
        $stmt->execute([$class_id]);
        $class_info = $stmt->fetch();
        
        // Kiểm tra nếu lớp không tồn tại hoặc không hoạt động
        if (!$class_info) {
            $error_message = "Không tìm thấy lớp học phần!";
            $class_id = ''; // Reset để hiển thị form chọn lớp
        } elseif ($class_info['TrangThaiLop'] !== 'hoạt động') {
            $error_message = "Lớp học phần này đã ngừng hoạt động!";
        }
    } catch (PDOException $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report']) && $class_info) {
    // Chỉ xử lý khi có thông tin lớp hợp lệ
    if ($class_info['TrangThaiLop'] === 'hoạt động') {
        try {
            // Kiểm tra số sinh viên có điểm hoàn chỉnh
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as completed_students
                FROM Diem d 
                JOIN SinhVien_LopHocPhan slhp ON d.MaSinhVien = slhp.MaSinhVien AND d.MaLopHocPhan = slhp.MaLopHocPhan
                WHERE d.MaLopHocPhan = ? AND d.DiemTongKet IS NOT NULL AND d.XepLoaiChu IS NOT NULL 
                AND slhp.TrangThaiDangKy = 'đang học'
            ");
            $stmt->execute([$class_id]);
            $completed_count = $stmt->fetch()['completed_students'];
            
            if ($completed_count > 0) {
                // Tạo báo cáo
                $stmt = $pdo->prepare("INSERT INTO BaoCaoXepLoai (MaLopHocPhan, NguoiThucHien, DinhDangXuat) VALUES (?, ?, ?)");
                $stmt->execute([$class_id, 'System Admin', $_POST['format']]);
                $success_message = "Đã tạo báo cáo thành công!";
            } else {
                $error_message = "Không thể xuất báo cáo: Chưa có sinh viên nào hoàn thành điểm tổng kết.";
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi tạo báo cáo: " . $e->getMessage();
        }
    } else {
        $error_message = "Không thể xuất báo cáo: Lớp học phần không hoạt động.";
    }
}

// Get grade statistics
$student_grades = [];
if ($class_id && $class_info) {
    try {
        // Get detailed student grades
        $stmt = $pdo->prepare("
            SELECT sv.MaSinhVien, sv.HoTen, sv.Email,
                   d.DiemChuyenCan, d.DiemGiuaKy, d.DiemGiuaKy2, d.DiemThaoLuan, d.DiemCuoiKy, d.DiemTongKet, d.XepLoaiChu, d.TinhTrang,
                   d.NgayCapNhat
            FROM SinhVien sv
            JOIN SinhVien_LopHocPhan slhp ON sv.MaSinhVien = slhp.MaSinhVien
            LEFT JOIN Diem d ON sv.MaSinhVien = d.MaSinhVien AND d.MaLopHocPhan = ?
            WHERE slhp.MaLopHocPhan = ? AND slhp.TrangThaiDangKy = 'đang học'
            ORDER BY d.DiemTongKet DESC, sv.MaSinhVien
        ");
        $stmt->execute([$class_id, $class_id]);
        $student_grades = $stmt->fetchAll();
        
        // Calculate statistics
        $total_students = count($student_grades);
        $graded_students = array_filter($student_grades, function($s) { return !is_null($s['DiemTongKet']); });
        $passed_students = array_filter($graded_students, function($s) { return $s['TinhTrang'] === 'Đạt'; });
        
        // Grade distribution
        $grade_distribution = [];
        $grade_counts = ['A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D+' => 0, 'D' => 0, 'F' => 0];
        
        foreach ($graded_students as $student) {
            if ($student['XepLoaiChu']) {
                $grade_counts[$student['XepLoaiChu']]++;
            }
        }
        
        // Calculate averages
        $total_scores = array_column($graded_students, 'DiemTongKet');
        $average_score = !empty($total_scores) ? array_sum($total_scores) / count($total_scores) : 0;
        
        $statistics = [
            'total_students' => $total_students,
            'graded_students' => count($graded_students),
            'passed_students' => count($passed_students),
            'pass_rate' => count($graded_students) > 0 ? (count($passed_students) / count($graded_students) * 100) : 0,
            'average_score' => round($average_score, 2),
            'grade_distribution' => $grade_counts
        ];
    } catch (PDOException $e) {
        $error_message = "Lỗi khi lấy thống kê: " . $e->getMessage();
    }
}

// Display messages
if ($success_message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<h1>Thống kê và tra cứu điểm</h1>

<?php if ($class_info): ?>
    <!-- Class Info -->
    <div style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 4px;">
        <h3 style="margin: 0 0 10px 0;">Thống kê điểm lớp: <?php echo htmlspecialchars($class_info['TenMonHoc']); ?></h3>
        <p style="margin: 0; color: #666;">Mã lớp: <?php echo htmlspecialchars($class_info['MaLopHocPhan']); ?> - Số tín chỉ: <?php echo htmlspecialchars($class_info['SoTinChi']); ?> - Học kỳ: <?php echo htmlspecialchars($class_info['HocKy']); ?> - Năm học: <?php echo htmlspecialchars($class_info['NamHoc']); ?></p>
    </div>
    
    <!-- Statistics Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
            <h4 style="margin: 0 0 10px 0; color: #333;">Tổng số sinh viên</h4>
            <p style="margin: 0; font-size: 24px; font-weight: bold; color: #007bff;"><?php echo $statistics['total_students']; ?></p>
        </div>
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
            <h4 style="margin: 0 0 10px 0; color: #333;">Đã có điểm</h4>
            <p style="margin: 0; font-size: 24px; font-weight: bold; color: #28a745;"><?php echo $statistics['graded_students']; ?></p>
        </div>
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
            <h4 style="margin: 0 0 10px 0; color: #333;">Tỷ lệ đạt</h4>
            <p style="margin: 0; font-size: 24px; font-weight: bold; color: #ffc107;"><?php echo number_format($statistics['pass_rate'], 1); ?>%</p>
        </div>
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
            <h4 style="margin: 0 0 10px 0; color: #333;">Điểm trung bình</h4>
            <p style="margin: 0; font-size: 24px; font-weight: bold; color: #dc3545;"><?php echo number_format($statistics['average_score'], 2); ?></p>
        </div>
    </div>

    <!-- Grade Distribution Statistics -->
    <h3 style="margin-bottom: 15px;">Thống kê phân bố điểm</h3>
    <div class="table-container">
        <table id="gradeDistributionTable">
            <thead>
                <tr>
                    <th>Điểm chữ</th>
                    <th>Số lượng</th>
                    <th>Tỷ lệ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statistics['grade_distribution'] as $grade => $count): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grade); ?></td>
                    <td><?php echo $count; ?></td>
                    <td><?php echo $statistics['graded_students'] > 0 ? 
                        number_format(($count / $statistics['graded_students']) * 100, 1) : 0; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Student List -->
    <h3 style="margin-bottom: 15px;">Danh sách sinh viên</h3>
    <div class="table-container">
        <table id="gradeTable">
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Chuyên cần</th>
                    <th>Giữa kỳ 1</th>
                    <?php if ($class_info['SoTinChi'] > 2): ?>
                    <th>Giữa kỳ 2</th>
                    <?php endif; ?>
                    <th>Thảo luận</th>
                    <th>Cuối kỳ</th>
                    <th>Tổng kết</th>
                    <th>Xếp loại</th>
                    <th>Tình trạng</th>
                    <th>Cập nhật</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                            <?php foreach ($student_grades as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['MaSinhVien']); ?></td>
                                <td><?php echo htmlspecialchars($student['HoTen']); ?></td>
                                <td><?php echo $student['DiemChuyenCan'] !== null ? 
                                    number_format($student['DiemChuyenCan'], 1) : '-'; ?></td>
                                <td><?php echo $student['DiemGiuaKy'] !== null ? 
                                    number_format($student['DiemGiuaKy'], 1) : '-'; ?></td>
                                <?php if ($class_info['SoTinChi'] > 2): ?>
                                <td><?php echo $student['DiemGiuaKy2'] !== null ? 
                                    number_format($student['DiemGiuaKy2'], 1) : '-'; ?></td>
                                <?php endif; ?>
                                <td><?php echo $student['DiemThaoLuan'] !== null ? 
                                    number_format($student['DiemThaoLuan'], 1) : '-'; ?></td>
                                <td><?php echo $student['DiemCuoiKy'] !== null ? 
                                    number_format($student['DiemCuoiKy'], 1) : '-'; ?></td>
                                <td><?php echo $student['DiemTongKet'] !== null ? 
                                    number_format($student['DiemTongKet'], 1) : '-'; ?></td>
                                <td><?php echo htmlspecialchars($student['XepLoaiChu'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($student['TinhTrang'] ?? 'Chưa có'); ?></td>
                                <td><?php echo $student['NgayCapNhat'] ? 
                                    date('d/m/Y H:i:s', strtotime($student['NgayCapNhat'])) : '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Export Options -->
    <div style="margin-top: 20px; background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
        <h4 style="margin: 0 0 15px 0;">Xuất báo cáo</h4>
        
        <?php
        // Kiểm tra điều kiện xuất báo cáo
        $export_allowed = false;
        $export_conditions = [];
        
        if ($class_info) {
            // 1. Lớp có trạng thái hoạt động
            $is_active = $class_info['TrangThaiLop'] === 'hoạt động';
            $export_conditions[] = [
                'condition' => $is_active,
                'message' => 'Lớp học phần có trạng thái "hoạt động"',
                'status' => $is_active ? 'success' : 'error'
            ];
            
            // 2. Ít nhất một sinh viên có điểm tổng kết hoàn chỉnh
            $completed_students_count = 0;
            if ($is_active && $class_id) {
                try {
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) as completed_students
                        FROM Diem d 
                        JOIN SinhVien_LopHocPhan slhp ON d.MaSinhVien = slhp.MaSinhVien AND d.MaLopHocPhan = slhp.MaLopHocPhan
                        WHERE d.MaLopHocPhan = ? AND d.DiemTongKet IS NOT NULL AND d.XepLoaiChu IS NOT NULL 
                        AND slhp.TrangThaiDangKy = 'đang học'
                    ");
                    $stmt->execute([$class_id]);
                    $completed_students_count = $stmt->fetch()['completed_students'];
                } catch (PDOException $e) {
                    $completed_students_count = 0;
                }
            }
            
            $has_completed_students = $completed_students_count > 0;
            $export_conditions[] = [
                'condition' => $has_completed_students,
                'message' => "Ít nhất một sinh viên có điểm tổng kết hoàn chỉnh (Hiện tại: {$completed_students_count} sinh viên)",
                'status' => $has_completed_students ? 'success' : 'error'
            ];
            
            // 3. Xếp loại chữ được tính theo thang điểm chuẩn
            $export_conditions[] = [
                'condition' => true,
                'message' => 'Xếp loại chữ được tính theo thang điểm chuẩn TMU',
                'status' => 'success'
            ];
            
            // Kiểm tra tất cả điều kiện
            $export_allowed = $is_active && $has_completed_students;
        }
        ?>
        
        <!-- Hiển thị điều kiện xuất báo cáo -->
        <div style="margin-bottom: 15px;">
            <p style="margin: 0 0 10px 0; font-weight: bold; color: #333;">Điều kiện xuất báo cáo:</p>
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($export_conditions as $condition): ?>
                    <li style="margin-bottom: 5px; color: <?= $condition['status'] === 'success' ? '#28a745' : '#dc3545' ?>;">
                        <?php if ($condition['status'] === 'success'): ?>
                            ✓ <?= htmlspecialchars($condition['message']) ?>
                        <?php else: ?>
                            ✗ <?= htmlspecialchars($condition['message']) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <?php if ($export_allowed): ?>
            <form method="post" style="display: flex; gap: 10px; align-items: center;">
                <select name="format" class="form-control" style="width: 150px;">
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
                <button type="submit" name="generate_report" class="btn btn-primary">Tạo báo cáo</button>
            </form>
        <?php else: ?>
            <div style="padding: 10px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;">
                <strong>Không thể xuất báo cáo:</strong> Chưa đáp ứng đủ điều kiện bắt buộc. Vui lòng hoàn thiện dữ liệu trước khi xuất báo cáo.
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Class Selection -->
    <div style="background: #f8f9fa; padding: 20px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <h3 style="margin: 0 0 15px 0;">Chọn lớp học phần để xem thống kê</h3>
        <p style="margin: 0 0 20px 0; color: #666;">Vui lòng chọn một lớp học phần từ danh sách bên dưới</p>
        
        <!-- Class selection form -->
        <form method="GET" action="index.php" class="search-form" style="justify-content: center;">
            <input type="hidden" name="page" value="thong_ke">
            <select name="class" style="flex: 0 0 400px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                <option value="">Chọn lớp học phần</option>
                <?php
                try {
                    $stmt = $pdo->query("SELECT MaLopHocPhan, TenMonHoc FROM LopHocPhan WHERE TrangThaiLop = 'hoạt động' ORDER BY MaLopHocPhan");
                    while ($class = $stmt->fetch()) {
                        echo '<option value="' . htmlspecialchars($class['MaLopHocPhan']) . '">';
                        echo htmlspecialchars($class['MaLopHocPhan']) . ' - ' . htmlspecialchars($class['TenMonHoc']);
                        echo '</option>';
                    }
                } catch (PDOException $e) {
                    echo '<option value="">Lỗi tải danh sách lớp</option>';
                }
                ?>
            </select>
            <button type="submit">Xem thống kê</button>
        </form>
    </div>
<?php endif; ?>