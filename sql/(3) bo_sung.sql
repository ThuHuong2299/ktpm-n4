-- Add SoTinChi column to LopHocPhan table and update sample data

USE QuanLyDiem;

-- Add SoTinChi column to LopHocPhan table
ALTER TABLE LopHocPhan 
ADD COLUMN SoTinChi TINYINT NOT NULL DEFAULT 3 
AFTER TenMonHoc;

-- Update existing data with appropriate credit hours
UPDATE LopHocPhan SET SoTinChi = 3 WHERE MaLopHocPhan = '251_EECIT3021_01'; -- Lập trình Web
UPDATE LopHocPhan SET SoTinChi = 3 WHERE MaLopHocPhan = '251_EECIT3021_02'; -- Lập trình Web
UPDATE LopHocPhan SET SoTinChi = 4 WHERE MaLopHocPhan = '251_MATB2023_01'; -- Toán cao cấp A2
UPDATE LopHocPhan SET SoTinChi = 3 WHERE MaLopHocPhan = '251_PHYS1014_01'; -- Vật lý đại cương
UPDATE LopHocPhan SET SoTinChi = 3 WHERE MaLopHocPhan = '252_EECIT3022_01'; -- Cơ sở dữ liệu
UPDATE LopHocPhan SET SoTinChi = 3 WHERE MaLopHocPhan = '252_EECIT3022_02'; -- Cơ sở dữ liệu
UPDATE LopHocPhan SET SoTinChi = 3 WHERE MaLopHocPhan = '252_EECIT3030_01'; -- Mạng máy tính
UPDATE LopHocPhan SET SoTinChi = 2 WHERE MaLopHocPhan = '251_ENGL1015_01'; -- Tiếng Anh chuyên ngành
UPDATE LopHocPhan SET SoTinChi = 6 WHERE MaLopHocPhan = '252_EECIT4001_01'; -- Đồ án tốt nghiệp
UPDATE LopHocPhan SET SoTinChi = 3 WHERE MaLopHocPhan = '252_MATB2024_01'; -- Xác suất thống kê

-- Verify the changes
SELECT MaLopHocPhan, TenMonHoc, SoTinChi, HocKy, GiangVienPhuTrach 
FROM LopHocPhan 
ORDER BY MaLopHocPhan;

-- Display summary by credit hours
SELECT SoTinChi, COUNT(*) as SoLuongLop, GROUP_CONCAT(TenMonHoc SEPARATOR ', ') as CacMonHoc
FROM LopHocPhan 
GROUP BY SoTinChi 
ORDER BY SoTinChi;