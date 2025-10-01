-- Sample data for QuanLyDiem database
-- Run this after creating the database structure

USE QuanLyDiem;

-- Insert sample data into LopHocPhan
INSERT INTO LopHocPhan (MaLopHocPhan, TenMonHoc, HocKy, NamHoc, GiangVienPhuTrach, TrangThaiLop, LyDoCapNhatXoa) VALUES
('251_EECIT3021_01', 'Lập trình Web', 1, '2025-2026', 'TS. Nguyễn Văn An', 'hoạt động', NULL),
('251_EECIT3021_02', 'Lập trình Web', 1, '2025-2026', 'TS. Trần Thị Bình', 'hoạt động', NULL),
('251_MATB2023_01', 'Toán cao cấp A2', 1, '2025-2026', 'PGS.TS. Lê Văn Cường', 'hoạt động', NULL),
('251_PHYS1014_01', 'Vật lý đại cương', 1, '2025-2026', 'TS. Phạm Thị Dung', 'hoạt động', NULL),
('252_EECIT3022_01', 'Cơ sở dữ liệu', 2, '2025-2026', 'TS. Hoàng Văn Em', 'hoạt động', NULL),
('252_EECIT3022_02', 'Cơ sở dữ liệu', 2, '2025-2026', 'ThS. Vũ Thị Phương', 'hoạt động', NULL),
('252_EECIT3030_01', 'Mạng máy tính', 2, '2025-2026', 'TS. Đỗ Văn Giang', 'hoạt động', NULL),
('251_ENGL1015_01', 'Tiếng Anh chuyên ngành', 1, '2025-2026', 'ThS. Bùi Thị Hoa', 'hoạt động', NULL),
('252_EECIT4001_01', 'Đồ án tốt nghiệp', 2, '2025-2026', 'TS. Lý Văn Kiên', 'hoạt động', NULL),
('252_MATB2024_01', 'Xác suất thống kê', 2, '2025-2026', 'PGS.TS. Cao Thị Lan', 'hoạt động', NULL);

-- Insert sample data into SinhVien
INSERT INTO SinhVien (MaSinhVien, HoTen, Email, NgaySinh) VALUES
('21D192059', 'Nguyễn Văn Hùng', 'hung.nv@tmu.edu.vn', '2003-03-15'),
('21D184726', 'Trần Thị Linh', 'linh.tt@tmu.edu.vn', '2003-07-22'),
('21D156892', 'Lê Minh Tuấn', 'tuan.lm@tmu.edu.vn', '2003-01-10'),
('21D173045', 'Phạm Thị Mai', 'mai.pt@tmu.edu.vn', '2003-11-08'),
('21D198167', 'Hoàng Văn Nam', 'nam.hv@tmu.edu.vn', '2003-05-18'),
('21D142738', 'Vũ Thị Hương', 'huong.vt@tmu.edu.vn', '2003-09-25'),
('21D165924', 'Đỗ Minh Quang', 'quang.dm@tmu.edu.vn', '2003-12-03'),
('21D187451', 'Bùi Thị Thảo', 'thao.bt@tmu.edu.vn', '2003-04-17'),
('21D153680', 'Lý Văn Đức', 'duc.lv@tmu.edu.vn', '2003-08-30'),
('21D179326', 'Cao Thị Nga', 'nga.ct@tmu.edu.vn', '2003-02-14'),
('21D164517', 'Phan Văn Sơn', 'son.pv@tmu.edu.vn', '2003-06-20'),
('21D128493', 'Đinh Thị Lan', 'lan.dt@tmu.edu.vn', '2003-10-12'),
('21D195672', 'Ngô Minh Hải', 'hai.nm@tmu.edu.vn', '2003-01-28'),
('21D146285', 'Tạ Thị Hạnh', 'hanh.tt@tmu.edu.vn', '2003-07-05'),
('21D172908', 'Dương Văn Khang', 'khang.dv@tmu.edu.vn', '2003-11-22'),
('22D158734', 'Trương Văn Long', 'long.tv@tmu.edu.vn', '2004-03-08'),
('22D193526', 'Lưu Thị Nhung', 'nhung.lt@tmu.edu.vn', '2004-05-15'),
('22D147681', 'Võ Minh Phú', 'phu.vm@tmu.edu.vn', '2004-09-12'),
('22D185349', 'Mạc Thị Quyên', 'quyen.mt@tmu.edu.vn', '2004-01-25'),
('22D162074', 'Hồ Văn Tài', 'tai.hv@tmu.edu.vn', '2004-07-18');

-- Insert sample data into SinhVien_LopHocPhan
INSERT INTO SinhVien_LopHocPhan (MaLopHocPhan, MaSinhVien, NgayDangKy, TrangThaiDangKy, LyDoThayDoi) VALUES
-- Lớp Lập trình Web 01
('251_EECIT3021_01', '21D192059', '2025-09-05 08:15:30', 'đang học', NULL),
('251_EECIT3021_01', '21D184726', '2025-09-07 10:42:15', 'đang học', NULL),
('251_EECIT3021_01', '21D156892', '2025-09-03 14:20:45', 'đang học', NULL),
('251_EECIT3021_01', '21D173045', '2025-09-08 16:33:20', 'đang học', NULL),
('251_EECIT3021_01', '21D198167', '2025-09-02 09:18:55', 'đang học', NULL),
('251_EECIT3021_01', '21D142738', '2025-09-04 11:25:10', 'đã rút', 'Chuyển sang lớp khác'),
('251_EECIT3021_01', '21D165924', '2025-09-06 13:47:35', 'đang học', NULL),
('251_EECIT3021_01', '21D187451', '2025-09-09 15:12:40', 'đang học', NULL),

-- Lớp Lập trình Web 02
('251_EECIT3021_02', '21D153680', '2025-09-05 08:55:20', 'đang học', NULL),
('251_EECIT3021_02', '21D179326', '2025-09-07 12:30:45', 'đang học', NULL),
('251_EECIT3021_02', '21D164517', '2025-09-03 14:17:10', 'đang học', NULL),
('251_EECIT3021_02', '21D128493', '2025-09-08 16:42:25', 'đang học', NULL),
('251_EECIT3021_02', '21D195672', '2025-09-02 09:38:50', 'đang học', NULL),
('251_EECIT3021_02', '21D146285', '2025-09-04 11:15:35', 'đang học', NULL),
('251_EECIT3021_02', '21D172908', '2025-09-06 13:28:15', 'đang học', NULL),
('251_EECIT3021_02', '22D158734', '2025-09-09 15:52:40', 'đang học', NULL),

-- Lớp Toán cao cấp A2
('251_MATB2023_01', '21D192059', '2025-09-04 07:45:20', 'đang học', NULL),
('251_MATB2023_01', '21D156892', '2025-09-06 10:22:35', 'đang học', NULL),
('251_MATB2023_01', '21D198167', '2025-09-02 13:18:50', 'đang học', NULL),
('251_MATB2023_01', '21D165924', '2025-09-08 15:45:10', 'đang học', NULL),
('251_MATB2023_01', '21D153680', '2025-09-03 08:32:25', 'đang học', NULL),
('251_MATB2023_01', '21D164517', '2025-09-07 11:57:40', 'đang học', NULL),
('251_MATB2023_01', '21D195672', '2025-09-05 14:13:55', 'đang học', NULL),
('251_MATB2023_01', '21D172908', '2025-09-09 16:28:15', 'đang học', NULL),

-- Lớp Vật lý đại cương
('251_PHYS1014_01', '21D184726', '2025-09-03 09:15:30', 'đang học', NULL),
('251_PHYS1014_01', '21D173045', '2025-09-05 12:42:45', 'đang học', NULL),
('251_PHYS1014_01', '21D187451', '2025-09-07 15:18:20', 'đang học', NULL),
('251_PHYS1014_01', '21D179326', '2025-09-02 08:35:55', 'đang học', NULL),
('251_PHYS1014_01', '21D128493', '2025-09-04 11:52:10', 'đang học', NULL),
('251_PHYS1014_01', '21D146285', '2025-09-06 14:27:35', 'đang học', NULL),
('251_PHYS1014_01', '22D193526', '2025-09-08 16:43:50', 'đang học', NULL),
('251_PHYS1014_01', '22D147681', '2025-09-09 09:18:25', 'đang học', NULL),

-- Lớp Cơ sở dữ liệu 01
('252_EECIT3022_01', '21D192059', '2025-02-12 08:20:15', 'hoàn thành', NULL),
('252_EECIT3022_01', '21D184726', '2025-02-14 10:45:30', 'hoàn thành', NULL),
('252_EECIT3022_01', '21D156892', '2025-02-10 13:32:45', 'hoàn thành', NULL),
('252_EECIT3022_01', '21D173045', '2025-02-16 15:18:20', 'hoàn thành', NULL),
('252_EECIT3022_01', '21D198167', '2025-02-08 09:55:35', 'hoàn thành', NULL),
('252_EECIT3022_01', '21D165924', '2025-02-13 12:28:50', 'hoàn thành', NULL),
('252_EECIT3022_01', '21D187451', '2025-02-15 14:42:10', 'hoàn thành', NULL);

-- Insert sample data into Diem
INSERT INTO Diem (MaLopHocPhan, MaSinhVien, DiemChuyenCan, DiemGiuaKy, DiemCuoiKy, DiemTongKet, XepLoaiChu, TinhTrang, NgayCapNhat) VALUES
-- Điểm cho lớp Lập trình Web 01 (đang học - chưa có điểm cuối kỳ)
('251_EECIT3021_01', '21D192059', 9.0, 8.5, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_01', '21D184726', 8.5, 7.5, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_01', '21D156892', 9.5, 9.0, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_01', '21D173045', 8.0, 7.0, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_01', '21D198167', 7.5, 6.5, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_01', '21D165924', 9.0, 8.0, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_01', '21D187451', 8.5, 8.5, NULL, NULL, NULL, NULL, NULL),

-- Điểm cho lớp Lập trình Web 02
('251_EECIT3021_02', '21D153680', 8.0, 7.5, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_02', '21D179326', 9.0, 8.5, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_02', '21D164517', 7.5, 7.0, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_02', '21D128493', 8.5, 8.0, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_02', '21D195672', 9.5, 9.0, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_02', '21D146285', 7.0, 6.5, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_02', '21D172908', 8.0, 7.5, NULL, NULL, NULL, NULL, NULL),
('251_EECIT3021_02', '22D158734', 8.5, 8.0, NULL, NULL, NULL, NULL, NULL),

-- Điểm cho lớp Toán cao cấp A2
('251_MATB2023_01', '21D192059', 8.5, 8.0, NULL, NULL, NULL, NULL, NULL),
('251_MATB2023_01', '21D156892', 9.0, 8.5, NULL, NULL, NULL, NULL, NULL),
('251_MATB2023_01', '21D198167', 7.0, 6.5, NULL, NULL, NULL, NULL, NULL),
('251_MATB2023_01', '21D165924', 8.0, 7.5, NULL, NULL, NULL, NULL, NULL),
('251_MATB2023_01', '21D153680', 9.5, 9.0, NULL, NULL, NULL, NULL, NULL),
('251_MATB2023_01', '21D164517', 6.5, 6.0, NULL, NULL, NULL, NULL, NULL),
('251_MATB2023_01', '21D195672', 8.5, 8.0, NULL, NULL, NULL, NULL, NULL),
('251_MATB2023_01', '21D172908', 7.5, 7.0, NULL, NULL, NULL, NULL, NULL),

-- Điểm cho lớp Vật lý đại cương
('251_PHYS1014_01', '21D184726', 8.0, 7.5, NULL, NULL, NULL, NULL, NULL),
('251_PHYS1014_01', '21D173045', 7.5, 7.0, NULL, NULL, NULL, NULL, NULL),
('251_PHYS1014_01', '21D187451', 9.0, 8.5, NULL, NULL, NULL, NULL, NULL),
('251_PHYS1014_01', '21D179326', 8.5, 8.0, NULL, NULL, NULL, NULL, NULL),
('251_PHYS1014_01', '21D128493', 7.0, 6.5, NULL, NULL, NULL, NULL, NULL),
('251_PHYS1014_01', '21D146285', 8.5, 8.0, NULL, NULL, NULL, NULL, NULL),
('251_PHYS1014_01', '22D193526', 9.0, 8.5, NULL, NULL, NULL, NULL, NULL),
('251_PHYS1014_01', '22D147681', 7.5, 7.0, NULL, NULL, NULL, NULL, NULL),

-- Điểm cho lớp Cơ sở dữ liệu 01 (đã hoàn thành - có đầy đủ điểm)
('252_EECIT3022_01', '21D192059', 9.0, 8.5, 8.8, 8.69, 'A', 'Đạt', '2025-06-18 14:25:15'),
('252_EECIT3022_01', '21D184726', 8.5, 7.5, 7.8, 7.74, 'B', 'Đạt', '2025-06-18 14:28:30'),
('252_EECIT3022_01', '21D156892', 9.5, 9.0, 9.2, 9.18, 'A', 'Đạt', '2025-06-18 14:31:45'),
('252_EECIT3022_01', '21D173045', 8.0, 7.0, 7.5, 7.35, 'B', 'Đạt', '2025-06-18 14:35:20'),
('252_EECIT3022_01', '21D198167', 7.5, 6.5, 6.8, 6.70, 'C+', 'Đạt', '2025-06-18 14:38:55'),
('252_EECIT3022_01', '21D165924', 9.0, 8.0, 8.5, 8.40, 'B+', 'Đạt', '2025-06-18 14:42:10'),
('252_EECIT3022_01', '21D187451', 6.0, 5.5, 4.2, 4.77, 'D', 'Đạt', '2025-06-18 14:45:35');

-- Insert sample data into BaoCaoXepLoai
INSERT INTO BaoCaoXepLoai (MaLopHocPhan, NgayInBaoCao, NguoiThucHien, DinhDangXuat) VALUES
('252_EECIT3022_01', '2025-06-25 09:15:30', 'TS. Hoàng Văn Em', 'PDF'),
('252_EECIT3022_01', '2025-06-25 09:32:45', 'TS. Hoàng Văn Em', 'Excel'),
('251_EECIT3021_01', '2025-10-12 10:45:20', 'TS. Nguyễn Văn An', 'PDF'),
('251_EECIT3021_02', '2025-10-12 11:18:35', 'TS. Trần Thị Bình', 'Excel'),
('251_MATB2023_01', '2025-10-18 14:22:50', 'PGS.TS. Lê Văn Cường', 'PDF');

-- Cập nhật tất cả lớp học phần về 1 giảng viên
UPDATE LopHocPhan 
SET GiangVienPhuTrach = 'TS. Nguyễn Văn An';