# Báo Cáo Kiểm Thử Chức Năng F2 - Tính Điểm Tổng Kết Học Phần

## 8.3. Demo Kiểm Thử Tự Động Trên Công Cụ PHPUnit

### 8.3.1. Giới thiệu về kiểm thử tự động với PHPUnit

PHPUnit là framework kiểm thử đơn vị (unit testing) phổ biến nhất cho ngôn ngữ PHP, được phát triển bởi Sebastian Bergmann. Trong dự án Quản Lý Điểm của chúng tôi, PHPUnit được sử dụng để kiểm thử chức năng **F2 - Tính điểm tổng kết học phần và xếp loại kết quả học tập**.

Chức năng F2 bao gồm:
- Tính toán điểm tổng kết theo công thức khác nhau cho môn 2TC và 3TC+
- Xếp loại chữ từ A đến F theo thang điểm TMU
- Xác định trạng thái Đạt/Không đạt
- Làm tròn điểm và validation dữ liệu đầu vào

### 8.3.2. Các bước thực hiện kiểm thử tự động PHPUnit trên VS Code

#### **Bước 1: Tạo Project PHP và cài đặt PHPUnit**

Đầu tiên, tạo thư mục dự án và cài đặt môi trường:

1. **Tạo thư mục dự án:** Mở VS Code → Chọn File → Open Folder → Tạo và chọn thư mục "QuanLyDiem"
   
2. **Cài đặt PHPUnit:** Tải phpunit.phar về thư mục gốc dự án.

3. **Cấu trúc thư mục dự án:**
   ```
   QuanLyDiem/
   ├── sql/              (Các file tạo database)
   ├── tests/            (Thư mục chứa các file test)
   ├── config/           (File cấu hình database)
   ├── phpunit.xml       (File cấu hình PHPUnit)
   └── phpunit.phar      (File thực thi PHPUnit)
   ```

#### **Bước 2: Tạo file cấu hình PHPUnit (phpunit.xml)**

Tạo file `phpunit.xml` trong thư mục gốc để cấu hình PHPUnit:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.5/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         cacheDirectory=".phpunit.cache"
         colors="true"
         testdox="true"
         executionOrder="depends,defects"
         beStrictAboutOutputDuringTests="true"
         failOnRisky="true"
         failOnWarning="false">
    
    <testsuites>
        <testsuite name="KiemThuChucNangF2_TinhDiemTongKet">
            <directory suffix="Test.php">tests</directory>
        </testsuite>
    </testsuites>

    <php>
        <ini name="error_reporting" value="-1"/>
        <ini name="memory_limit" value="1G"/>
        <ini name="max_execution_time" value="300"/>
        <ini name="date.timezone" value="Asia/Ho_Chi_Minh"/>
        
        <env name="TEST_DATABASE" value="QuanLyDiem"/>
        <env name="TEST_ENVIRONMENT" value="testing"/>
    </php>
</phpunit>
```

Trong đó:
- **bootstrap**: Chỉ định file khởi tạo môi trường test
- **testdox**: Hiển thị kết quả test dưới dạng documentation
- **colors**: Bật màu sắc cho output dễ đọc
- **php section**: Cấu hình môi trường PHP cho test

*[Chụp ảnh: File phpunit.xml trong VS Code]*

#### **Bước 3: Tạo file bootstrap để cấu hình test (tests/bootstrap.php)**

Nhấp chuột phải vào thư mục dự án → New Folder → Đặt tên "tests"
Nhấp chuột phải vào thư mục tests → New File → Đặt tên "bootstrap.php"

File `bootstrap.php` được sử dụng để thiết lập môi trường test trước khi chạy:

```php
<?php
// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Thiết lập báo cáo lỗi đầy đủ cho môi trường test
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Định nghĩa đường dẫn gốc của dự án
define('ROOT_PATH', dirname(__DIR__));

// Hiển thị thông tin môi trường test
echo "PHPUnit Bootstrap: Môi trường kiểm thử tích hợp đã được khởi tạo!\n";
echo "Root Path: " . ROOT_PATH . "\n";
echo "Timezone: " . date_default_timezone_get() . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "==========================================\n";
```

Trong đó:
- **date_default_timezone_set**: Thiết lập múi giờ cho test
- **error_reporting**: Báo cáo tất cả lỗi PHP trong quá trình test
- **ROOT_PATH**: Định nghĩa đường dẫn gốc để sử dụng trong test
- **echo statements**: Hiển thị thông tin môi trường khi chạy test

*[Chụp ảnh: File bootstrap.php trong VS Code]*

#### **Bước 4: Tạo class test chính (tests/KiemThuChucNangF2Test.php)**

Nhấp chuột phải vào thư mục tests → New File → Đặt tên "KiemThuChucNangF2Test.php"

**Class KiemThuChucNangF2Test:** Để quản lý và thực hiện các ca kiểm thử cho chức năng F2 - Tính điểm tổng kết học phần. Class này kế thừa từ TestCase của PHPUnit để có thể sử dụng các method assertion.

```php
<?php
use PHPUnit\Framework\TestCase;

/**
 * Kiểm thử chức năng F2 - Tính điểm tổng kết học phần và xếp loại kết quả học tập
 * 
 * Mục tiêu: Kiểm tra tính chính xác của công thức tính điểm, xếp loại chữ và trạng thái đạt/không đạt
 * Phương pháp: Integration test với database MySQL thực tế
 */
class KiemThuChucNangF2Test extends TestCase
{
    private PDO $pdo;
    private string $testMaLopHocPhan = '251_EECIT3021_01'; // Lập trình Web (3TC)
    private string $testMaSinhVien = '21D156892'; // Lê Minh Tuấn
```

**Trong đó:**

➤ **Khai báo các thuộc tính private để đảm bảo tính bảo mật:**
- `$pdo` được khai báo private để lưu trữ kết nối database, đảm bảo chỉ các method trong class mới có thể truy cập và sử dụng kết nối này.
- `$testMaLopHocPhan` là mã lớp học phần dùng để test, được gán giá trị cố định '251_EECIT3021_01' (môn Lập trình Web - 3 tín chỉ).
- `$testMaSinhVien` là mã sinh viên dùng để test, được gán giá trị cố định '21D156892' (sinh viên Lê Minh Tuấn).

➤ **Method setUp() - Hàm khởi tạo môi trường test:**

```php
protected function setUp(): void
{
    // Kết nối database cho test
    try {
        $this->pdo = new PDO(
            "mysql:host=localhost;dbname=QuanLyDiem;charset=utf8mb4",
            'root', '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        $this->markTestSkipped('Không thể kết nối database: ' . $e->getMessage());
    }

    // Đảm bảo dữ liệu test tồn tại
    $this->kiemTraDuLieu();
}
```

Method `setUp()` được PHPUnit tự động gọi trước khi chạy mỗi test case, trong method này nhóm thực hiện:
- **Kết nối database:** Tạo kết nối PDO đến database QuanLyDiem với charset utf8mb4 để hỗ trợ tiếng Việt.
- **Xử lý lỗi:** Sử dụng try-catch để bắt lỗi kết nối, nếu không kết nối được sẽ skip test và thông báo lỗi.
- **Kiểm tra dữ liệu:** Gọi method `kiemTraDuLieu()` để đảm bảo dữ liệu test đã có trong database.

→ Khi khởi tạo đối tượng test mới, môi trường sẽ được thiết lập sẵn sàng để chạy các test case.

➤ **Method kiemTraDuLieu() - Kiểm tra tính hợp lệ của dữ liệu test:**

```php
private function kiemTraDuLieu(): void
{
    // Kiểm tra lớp học phần tồn tại
    $stmt = $this->pdo->prepare("SELECT * FROM LopHocPhan WHERE MaLopHocPhan = ?");
    if (!$stmt->execute([$this->testMaLopHocPhan]) || !$stmt->fetch()) {
        $this->markTestSkipped("Lớp {$this->testMaLopHocPhan} không tồn tại");
    }

    // Kiểm tra sinh viên tồn tại
    $stmt = $this->pdo->prepare("SELECT * FROM SinhVien WHERE MaSinhVien = ?");
    if (!$stmt->execute([$this->testMaSinhVien]) || !$stmt->fetch()) {
        $this->markTestSkipped("Sinh viên {$this->testMaSinhVien} không tồn tại");
    }
}
```

Method `kiemTraDuLieu()` sẽ kiểm tra các dữ liệu cần thiết cho test. Nếu dữ liệu không hợp lệ thì các thuộc tính sẽ được xử lý như sau:
- **Lớp học phần không tồn tại:** Test sẽ được skip và thông báo lỗi "Lớp XXX không tồn tại"
- **Sinh viên không tồn tại:** Test sẽ được skip và thông báo lỗi "Sinh viên XXX không tồn tại"

→ Điều này đảm bảo chỉ chạy test khi có đầy đủ dữ liệu cần thiết, tránh lỗi false negative.

*[Chụp ảnh: Cấu trúc class test trong VS Code]*

#### **Bước 5: Viết method helper và các test case cụ thể**

**5.1. Method helper để thao tác database:**

```php
/**
 * Helper: Thêm điểm và lấy kết quả
 */
private function themDiemVaLayKetQua($maLop, $maSV, $cc, $gk1, $gk2, $tl, $ck): array
{
    // Xóa điểm cũ nếu có
    $this->pdo->prepare("DELETE FROM Diem WHERE MaLopHocPhan = ? AND MaSinhVien = ?")
              ->execute([$maLop, $maSV]);

    // Thêm điểm mới vào database
    $this->pdo->prepare("INSERT INTO Diem (MaLopHocPhan, MaSinhVien, DiemChuyenCan, DiemGiuaKy, DiemGiuaKy2, DiemThaoLuan, DiemCuoiKy) VALUES (?, ?, ?, ?, ?, ?, ?)")
              ->execute([$maLop, $maSV, $cc, $gk1, $gk2, $tl, $ck]);

    // Lấy kết quả điểm đã được trigger tự động tính toán
    $stmt = $this->pdo->prepare("SELECT DiemTongKet, XepLoaiChu, TinhTrang FROM Diem WHERE MaLopHocPhan = ? AND MaSinhVien = ?");
    $stmt->execute([$maLop, $maSV]);
    return $stmt->fetch();
}
```

**5.2. Test Case 1 - Tính điểm môn 3 tín chỉ, Xếp loại A:**

```php
/**
 * Test 1: Tính điểm môn 3TC - Xếp loại A (điểm cao)
 */
public function testF2_Mon3TC_XepLoaiA(): void
{
    // Input: CC=9.0, GK1=8.5, GK2=9.0, TL=8.8, CK=8.7
    $diem = $this->themDiemVaLayKetQua(
        $this->testMaLopHocPhan, 
        $this->testMaSinhVien, 
        9.0, 8.5, 9.0, 8.8, 8.7
    );
    
    // Công thức 3TC: (CC×0.1) + ((GK1+GK2)/2×0.15) + (TL×0.15) + (CK×0.6)
    $expected = (9.0 * 0.1) + (((8.5 + 9.0) / 2) * 0.15) + (8.8 * 0.15) + (8.7 * 0.6);
    $expected = round($expected, 2); // = 8.64
    
    // Assertions kiểm tra kết quả
    $this->assertEqualsWithDelta($expected, $diem['DiemTongKet'], 0.01);
    $this->assertEquals('A', $diem['XepLoaiChu']);
    $this->assertEquals('Đạt', $diem['TinhTrang']);
}
```

**5.3. Test Case 2 - Tính điểm môn 2 tín chỉ, Xếp loại B:**

```php
/**
 * Test 2: Tính điểm môn 2TC - Xếp loại B
 */
public function testF2_Mon2TC_XepLoaiB(): void
{
    $maLop2TC = '251_ENGL1015_01'; // Tiếng Anh (2TC)
    
    // Input: CC=7.5, GK=7.0, TL=7.2, CK=7.5
    $diem = $this->themDiemVaLayKetQua($maLop2TC, $this->testMaSinhVien, 7.5, 7.0, null, 7.2, 7.5);
    
    // Công thức 2TC: (CC×0.1) + (GK×0.15) + (TL×0.15) + (CK×0.6)
    $expected = (7.5 * 0.1) + (7.0 * 0.15) + (7.2 * 0.15) + (7.5 * 0.6);
    $expected = round($expected, 2); // = 7.38
    
    $this->assertEqualsWithDelta($expected, $diem['DiemTongKet'], 0.01);
    $this->assertEquals('B', $diem['XepLoaiChu']);
    $this->assertEquals('Đạt', $diem['TinhTrang']);
}
```

**5.4. Test Cases cho các điểm biên xếp loại:**

- **Test 3:** Điểm biên 8.5 → Xếp loại A
- **Test 4:** Điểm biên 8.0 → Xếp loại B+  
- **Test 5:** Điểm biên 7.0 → Xếp loại B
- **Test 6:** Điểm biên 4.0 → Xếp loại D (Đạt)
- **Test 7:** Điểm < 4.0 → Xếp loại F (Không đạt)

*[Chụp ảnh: Các test case trong VS Code]*

#### **Bước 6: Chuẩn bị database và dữ liệu test**

**6.1. Tạo database và bảng:**

Mở MySQL/XAMPP → Tạo database "QuanLyDiem" → Import các file SQL theo thứ tự:
1. `sql/(1) create_database.sql` - Tạo cấu trúc bảng và trigger
2. `sql/(2) sample_data.sql` - Thêm dữ liệu mẫu
3. `sql/(3) bo_sung.sql` - Thêm cột SoTinChi
4. `sql/(4) update_diem.sql` - Cập nhật công thức tính điểm TMU

**6.2. Kiểm tra dữ liệu:**

```sql
-- Kiểm tra lớp học phần test
SELECT * FROM LopHocPhan WHERE MaLopHocPhan = '251_EECIT3021_01';

-- Kiểm tra sinh viên test  
SELECT * FROM SinhVien WHERE MaSinhVien = '21D156892';
```

#### **Bước 7: Chạy test bằng command line**

Mở Terminal trong VS Code (Ctrl + `) và chạy các lệnh:

**7.1. Chạy tất cả test:**
```bash
php phpunit.phar
```

**7.2. Chạy test cụ thể một file:**
```bash
php phpunit.phar tests/KiemThuChucNangF2Test.php
```

**7.3. Chạy test với output verbose:**
```bash
php phpunit.phar --testdox tests/KiemThuChucNangF2Test.php
```

**7.4. Chạy test một method cụ thể:**
```bash
php phpunit.phar --filter testF2_Mon3TC_XepLoaiA tests/KiemThuChucNangF2Test.php
```

*[Chụp ảnh: Terminal VS Code với các lệnh chạy test]*

### 8.3.3. Kết Quả Kiểm Thử

#### **8.3.3.1. Kết quả test thành công (PASS)**

Khi chạy lệnh `php phpunit.phar --testdox tests/KiemThuChucNangF2Test.php`, kết quả hiển thị:

```
PHPUnit Bootstrap: Môi trường kiểm thử tích hợp đã được khởi tạo!
Root Path: C:\Users\Admin\Desktop\Năm ba\QuanLyDiem
Timezone: Asia/Ho_Chi_Minh
PHP Version: 8.1.10
==========================================

PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.1.10
Configuration: C:\Users\Admin\Desktop\Năm ba\QuanLyDiem\phpunit.xml

..........                                                        10 / 10 (100%)

Time: 00:00.272, Memory: 24.00 MB

Kiem Thu Chuc Nang F2 (KiemThuChucNangF2Test)
 ✔ F2 Mon3TC XepLoaiA
 ✔ F2 Mon2TC XepLoaiB  
 ✔ F2 DiemBien XepLoaiA 85
 ✔ F2 DiemBien XepLoaiBPlus 80
 ✔ F2 DiemBien XepLoaiB 70
 ✔ F2 DiemBien Dat 40
 ✔ F2 DiemKhongDat
 ✔ F2 DiemSanCuoiKy
 ✔ F2 DiemCaoNhat
 ✔ F2 LamTronDiem

OK (10 tests, 30 assertions)
```

**Giải thích kết quả:**
- **10/10 (100%)**: Tất cả 10 test case đều PASS thành công
- **Time: 00:00.272**: Thời gian thực thi test rất nhanh (272ms)
- **Memory: 24.00 MB**: Bộ nhớ sử dụng hợp lý
- **30 assertions**: Tổng số assertion được kiểm tra trong tất cả test case

#### **8.3.3.2. Chi tiết từng test case**

**Test 1 - Mon3TC XepLoaiA:** ✓ PASS
- Input: CC=9.0, GK1=8.5, GK2=9.0, TL=8.8, CK=8.7
- Expected: 8.64, Xếp loại A, Đạt
- Actual: 8.64, A, Đạt → **Khớp hoàn toàn**

**Test 2 - Mon2TC XepLoaiB:** ✓ PASS  
- Input: CC=7.5, GK=7.0, TL=7.2, CK=7.5 (môn 2TC)
- Expected: 7.38, Xếp loại B, Đạt
- Actual: 7.38, B, Đạt → **Khớp hoàn toàn**

**Test 3-6 - DiemBien:** ✓ PASS
- Kiểm tra các mốc xếp loại: 8.5→A, 8.0→B+, 7.0→B, 4.0→D
- Tất cả đều cho kết quả chính xác theo quy định TMU

**Test 7 - DiemKhongDat:** ✓ PASS
- Điểm < 4.0 được xếp loại F và trạng thái "Không đạt"

**Test 8-10 - CacTruongHopDacBiet:** ✓ PASS
- Kiểm tra điểm sàn, điểm cao nhất, làm tròn đều chính xác

*[Chụp ảnh: Terminal VS Code hiển thị kết quả test PASS]*

#### **8.3.3.3. Phân tích chức năng được kiểm thử**

**Chức năng F2 - Tính điểm tổng kết học phần** đã được kiểm thử toàn diện qua 10 test case:

**A. Công thức tính điểm:**
- **Môn 2 tín chỉ:** (CC × 0.1) + (GK × 0.15) + (TL × 0.15) + (CK × 0.6)
- **Môn 3+ tín chỉ:** (CC × 0.1) + ((GK1+GK2)/2 × 0.15) + (TL × 0.15) + (CK × 0.6)

**B. Thang xếp loại TMU:**
| Điểm | Xếp loại | Trạng thái |
|------|----------|------------|
| ≥ 8.5 | A | Đạt |
| ≥ 8.0 | B+ | Đạt |
| ≥ 7.0 | B | Đạt |
| ≥ 6.5 | C+ | Đạt |
| ≥ 5.5 | C | Đạt |
| ≥ 5.0 | D+ | Đạt |
| ≥ 4.0 | D | Đạt |
| < 4.0 | F | Không đạt |

**C. Validation và xử lý dữ liệu:**
- Làm tròn điểm đến 2 chữ số thập phân
- Xử lý trường hợp điểm NULL
- Kiểm tra ràng buộc điểm từ 0.0 đến 10.0

### 8.3.4. Tổng Kết

Việc áp dụng kiểm thử tự động PHPUnit cho chức năng F2 đã mang lại những lợi ích to lớn:

**1. Đảm bảo chính xác:** Tất cả công thức tính điểm và quy tắc xếp loại đều được kiểm tra tự động, tránh sai sót trong quá trình phát triển.

**2. Tăng hiệu quả:** Thay vì kiểm tra thủ công từng trường hợp, 10 test case được thực thi tự động chỉ trong 272ms.

**3. Regression Testing:** Khi có thay đổi code, chỉ cần chạy lại test để đảm bảo chức năng cũ không bị ảnh hưởng.

**4. Documentation:** Các test case đóng vai trò như documentation sống, mô tả rõ ràng cách thức hoạt động của chức năng.

**5. Confidence:** Đội ngũ phát triển có thể tự tin triển khai tính năng mới khi đã có bộ test đầy đủ bao phủ các trường hợp.

Phương pháp này có thể được áp dụng tương tự cho các chức năng khác trong hệ thống Quản Lý Điểm, tạo nên một bộ test suite toàn diện đảm bảo chất lượng phần mềm.
