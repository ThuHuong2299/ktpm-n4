USE QuanLyDiem;

-- Thêm 3 sinh viên mới vào bảng SinhVien
INSERT INTO SinhVien (MaSinhVien, HoTen, Email, NgaySinh) VALUES
('22D174523', 'Nguyễn Minh Đức', 'duc.nm@tmu.edu.vn', '2004-03-12'),
('22D186947', 'Trần Thị Hằng', 'hang.tt@tmu.edu.vn', '2004-08-20'),
('22D159368', 'Lê Văn Hoàng', 'hoang.lv@tmu.edu.vn', '2004-05-07'),
('22D167829', 'Phạm Văn Thành', 'thanh.pv@tmu.edu.vn', '2004-02-14'),
('22D195431', 'Đặng Thị Liên', 'lien.dt@tmu.edu.vn', '2004-06-28'),
('22D138765', 'Hoàng Minh Tuấn', 'tuan.hm@tmu.edu.vn', '2004-09-15'),
('22D172654', 'Vương Thị Ánh', 'anh.vt@tmu.edu.vn', '2004-04-03'),
('22D184293', 'Lê Đức Anh', 'anh.ld@tmu.edu.vn', '2004-11-22');

-- Đăng ký 3 sinh viên mới vào lớp Xác suất thống kê
INSERT INTO SinhVien_LopHocPhan (MaLopHocPhan, MaSinhVien, NgayDangKy, TrangThaiDangKy, LyDoThayDoi) VALUES
('252_MATB2024_01', '22D174523', '2025-02-15 09:30:20', 'đang học', NULL),
('252_MATB2024_01', '22D186947', '2025-02-16 11:15:45', 'đang học', NULL),
('252_MATB2024_01', '22D159368', '2025-02-17 14:22:10', 'đang học', NULL);

-- Thêm điểm chuyên cần và giữa kỳ cho 3 sinh viên mới (chưa có điểm cuối kỳ)
INSERT INTO Diem (MaLopHocPhan, MaSinhVien, DiemChuyenCan, DiemGiuaKy, DiemCuoiKy, DiemTongKet, XepLoaiChu, TinhTrang, NgayCapNhat) VALUES
('252_MATB2024_01', '22D174523', 8.5, 7.8, NULL, NULL, NULL, NULL, NULL),
('252_MATB2024_01', '22D186947', 9.0, 8.2, NULL, NULL, NULL, NULL, NULL),
('252_MATB2024_01', '22D159368', 7.5, 7.0, NULL, NULL, NULL, NULL, NULL);

-- Đăng ký sinh viên mới vào lớp Tiếng Anh chuyên ngành
INSERT INTO SinhVien_LopHocPhan (MaLopHocPhan, MaSinhVien, NgayDangKy, TrangThaiDangKy, LyDoThayDoi) VALUES
('251_ENGL1015_01', '22D167829', '2025-09-10 08:45:30', 'đang học', NULL),
('251_ENGL1015_01', '22D195431', '2025-09-11 10:22:15', 'đang học', NULL),
('251_ENGL1015_01', '22D138765', '2025-09-12 14:18:45', 'đang học', NULL),
('251_ENGL1015_01', '22D172654', '2025-09-13 09:35:20', 'đang học', NULL),
('251_ENGL1015_01', '22D184293', '2025-09-14 16:42:10', 'đang học', NULL);

-- Thêm điểm chuyên cần, giữa kỳ và thảo luận cho sinh viên mới (chưa có điểm cuối kỳ)
-- Môn Tiếng Anh chuyên ngành (2 tín chỉ) sử dụng công thức: (CC × 0.1) + (GK × 0.15) + (TL × 0.15) + (CK × 0.6)
INSERT INTO Diem (MaLopHocPhan, MaSinhVien, DiemChuyenCan, DiemGiuaKy, DiemThaoLuan, DiemCuoiKy, DiemTongKet, XepLoaiChu, TinhTrang, NgayCapNhat) VALUES
('251_ENGL1015_01', '22D167829', 8.0, 7.5, 8.5, NULL, NULL, NULL, NULL, NULL),
('251_ENGL1015_01', '22D195431', 9.5, 8.8, 9.0, NULL, NULL, NULL, NULL, NULL),
('251_ENGL1015_01', '22D138765', 7.0, 6.5, 7.5, NULL, NULL, NULL, NULL, NULL),
('251_ENGL1015_01', '22D172654', 8.5, 8.0, 8.2, NULL, NULL, NULL, NULL, NULL),
('251_ENGL1015_01', '22D184293', 6.5, 6.0, 7.0, NULL, NULL, NULL, NULL, NULL);
