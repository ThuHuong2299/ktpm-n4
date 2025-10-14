<?php
// Check if database connection exists
if (!isset($conn)) {
    die("Không có kết nối cơ sở dữ liệu");
}

// Get search parameter and build query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = "WHERE TrangThaiLop = N'hoạt động'";

// Add search condition if search term is provided
if (!empty($search)) {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND (
        MaLopHocPhan LIKE '%" . $searchEscaped . "%' OR
        TenMonHoc LIKE N'%" . $searchEscaped . "%'
    )";
}

// Query to get class list with export readiness check
$query = "SELECT l.MaLopHocPhan as ma_lop, l.TenMonHoc as ten_mon, l.SoTinChi as so_tin_chi, 
                 l.HocKy as hoc_ky, l.NamHoc as nam_hoc, l.GiangVienPhuTrach as giang_vien,
                 COUNT(DISTINCT slhp.MaSinhVien) as total_students,
                 COUNT(DISTINCT CASE WHEN d.DiemTongKet IS NOT NULL AND d.XepLoaiChu IS NOT NULL THEN d.MaSinhVien END) as completed_students
          FROM LopHocPhan l
          LEFT JOIN SinhVien_LopHocPhan slhp ON l.MaLopHocPhan = slhp.MaLopHocPhan AND slhp.TrangThaiDangKy = 'đang học'
          LEFT JOIN Diem d ON slhp.MaSinhVien = d.MaSinhVien AND slhp.MaLopHocPhan = d.MaLopHocPhan
          $whereClause
          GROUP BY l.MaLopHocPhan, l.TenMonHoc, l.SoTinChi, l.HocKy, l.NamHoc, l.GiangVienPhuTrach
          ORDER BY l.NamHoc DESC, l.HocKy DESC";
$result = mysqli_query($conn, $query);
?>

<h1>Quản lý điểm sinh viên</h1>

<!-- Quick Actions -->
<div style="margin-bottom: 20px;">
    <a href="index.php?page=quan_ly_lop&action=add" class="btn btn-primary" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">+ Thêm lớp mới</a>
    <a href="index.php?page=quan_ly_lop" class="btn btn-secondary" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Quản lý lớp</a>
</div>

<!-- Search Form -->
<form class="search-form" method="GET" action="">
    <input type="hidden" name="page" value="trang_chu">
    <input type="text" name="search" id="searchInput" placeholder="Nhập mã lớp hoặc tên môn học" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <button type="submit">Tra cứu</button>
    <?php if (!empty($search)): ?>
        <a href="index.php?page=trang_chu" class="btn" style="background-color: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-left: 5px;">Xóa tìm kiếm</a>
    <?php endif; ?>
</form>

<?php if (!empty($search)): ?>
    <div class="search-info" style="margin-bottom: 15px; padding: 10px; background-color: #e3f2fd; border-left: 4px solid #2196f3; color: #0d47a1;">
        <strong>Tìm kiếm cho:</strong> "<?php echo htmlspecialchars($search); ?>" - 
        <strong>Tìm thấy <?php echo mysqli_num_rows($result); ?> kết quả</strong>
    </div>
<?php endif; ?>

<!-- Table -->
<div class="table-container">
    <table id="gradeTable">
        <thead>
            <tr>
                <th>Mã lớp</th>
                <th>Tên môn</th>
                <th>Số tín chỉ</th>
                <th>Học kỳ</th>
                <th>Năm học</th>
                <th>Sinh viên</th>
                <th>Trạng thái xuất BC</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Kiểm tra điều kiện xuất báo cáo
                    $can_export = ($row['completed_students'] > 0);
                    $export_status = $can_export ? 'Sẵn sàng' : 'Chưa sẵn sàng';
                    $export_color = $can_export ? '#28a745' : '#dc3545';
                    $export_icon = $can_export ? '✓' : '✗';
                    
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ma_lop']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ten_mon']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['so_tin_chi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['hoc_ky']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nam_hoc']) . "</td>";
                    echo "<td style='text-align: center;'>";
                    echo "<span style='color: #666;'>" . $row['completed_students'] . "/" . $row['total_students'] . "</span>";
                    echo "<br><small style='color: #999;'>Hoàn thành</small>";
                    echo "</td>";
                    echo "<td style='text-align: center;'>";
                    echo "<span style='color: {$export_color}; font-weight: bold;'>{$export_icon} {$export_status}</span>";
                    if (!$can_export && $row['total_students'] > 0) {
                        echo "<br><small style='color: #999;'>Cần nhập điểm</small>";
                    } elseif ($row['total_students'] == 0) {
                        echo "<br><small style='color: #999;'>Chưa có SV</small>";
                    }
                    echo "</td>";
                    echo "<td>
                            <div class='action-buttons'>
                                <a href='index.php?page=thong_ke&class=" . htmlspecialchars($row['ma_lop']) . "' class='btn btn-view'>Xem thống kê</a>
                                <a href='index.php?page=quan_ly_lop&action=edit&id=" . htmlspecialchars($row['ma_lop']) . "' class='btn btn-edit'>Cập nhật</a>
                                <button onclick='deleteClass(\"" . htmlspecialchars($row['ma_lop']) . "\")' class='btn btn-delete'>Xóa</button>
                            </div>
                          </td>";
                    echo "</tr>";
                }
            } else {
                $emptyMessage = !empty($search) 
                    ? "Không tìm thấy lớp nào phù hợp với từ khóa \"" . htmlspecialchars($search) . "\""
                    : "Không tìm thấy dữ liệu";
                echo "<tr><td colspan='8' class='empty-message'>$emptyMessage</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
function deleteClass(id) {
    if (confirm('Bạn có chắc chắn muốn xóa lớp ' + id + '?')) {
        window.location.href = 'index.php?page=quan_ly_lop&action=delete&lop=' + id;
    }
}
</script>