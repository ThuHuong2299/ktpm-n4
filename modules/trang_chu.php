<?php
// Kiểm tra kết nối database và hiển thị notification nếu lỗi
if (!isset($conn)) {
    echo '<script>showError("Lỗi hệ thống: Không thể kết nối cơ sở dữ liệu. Vui lòng liên hệ quản trị viên!");</script>';
    $conn = null; // Set fallback để tránh crash
}

// Lấy và validate search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Validate độ dài search input và hiển thị notification nếu cần
if (strlen($search) > 100) {
    echo '<script>showWarning("Từ khóa tìm kiếm quá dài. Tối đa 100 ký tự.");</script>';
    $search = substr($search, 0, 100);
}

$whereClause = "WHERE TrangThaiLop = N'hoạt động'";

// Thêm điều kiện tìm kiếm nếu có từ khóa và connection hợp lệ
if (!empty($search) && $conn) {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND (
        MaLopHocPhan LIKE '%" . $searchEscaped . "%' OR
        TenMonHoc LIKE N'%" . $searchEscaped . "%'
    )";
}

// Truy vấn danh sách lớp học phần với error handling
$query = "SELECT MaLopHocPhan as ma_lop, TenMonHoc as ten_mon, SoTinChi as so_tin_chi, 
                 HocKy as hoc_ky, NamHoc as nam_hoc, GiangVienPhuTrach as giang_vien
          FROM LopHocPhan
          $whereClause
          ORDER BY NamHoc DESC, HocKy DESC";
$result = $conn ? mysqli_query($conn, $query) : false;

// Kiểm tra lỗi truy vấn và hiển thị notification
if (!$result && $conn) {
    echo '<script>showError("Lỗi truy vấn dữ liệu. Vui lòng thử lại sau!");</script>';
}
?>

<h1>Quản lý điểm sinh viên</h1>

<!-- Quick Actions -->
<div style="margin-bottom: 20px;">
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

<?php 
// Xử lý các trường hợp notification cho tìm kiếm
if (isset($_GET['search']) && empty(trim($_GET['search']))): ?>
    <script>showWarning('Vui lòng nhập từ khóa tìm kiếm trước khi thực hiện tra cứu');</script>
<?php elseif (!empty($search) && $result): 
    // Notification cho kết quả tìm kiếm
    $resultCount = mysqli_num_rows($result);
    if ($resultCount > 0): ?>
        <script>showInfo('Tìm thấy <?= $resultCount ?> kết quả cho "<?= addslashes($search) ?>"');</script>
    <?php else: ?>
        <script>showWarning('Không tìm thấy lớp học phần nào với từ khóa "<?= addslashes($search) ?>"');</script>
    <?php endif;
endif; ?>

<?php if (!empty($search) && $result): ?>
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
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php
            // Kiểm tra và hiển thị notification cho hệ thống trống (chỉ khi không search)
            if ($result && empty($search) && mysqli_num_rows($result) == 0):
                echo '<script>showInfo("Hệ thống chưa có lớp học phần nào. Vui lòng liên hệ quản trị viên để thêm dữ liệu.");</script>';
            endif;
            
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ma_lop']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ten_mon']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['so_tin_chi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['hoc_ky']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nam_hoc']) . "</td>";
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
                echo "<tr><td colspan='6' class='empty-message'>$emptyMessage</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
// Thêm loading notification cho search form
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            showInfo('Đang tìm kiếm...', 1000);
        });
    }
});

// Function xóa lớp - chặn quyền và hiển thị thông báo
function deleteClass(id) {
    // Hiển thị thông báo không có quyền xóa
    showError('Bạn không có quyền xóa lớp này. Vui lòng liên hệ quản trị viên để được hỗ trợ.', 5000);
    return false; // Ngăn không cho thực hiện xóa
}
</script>