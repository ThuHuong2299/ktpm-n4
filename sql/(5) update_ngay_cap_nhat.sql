-- Cập nhật thời gian cho các bản ghi điểm hiện tại (không tính dữ liệu mẫu)
-- Script này sẽ cập nhật NgayCapNhat thành thời điểm hiện tại cho tất cả các bản ghi điểm có sẵn

USE QuanLyDiem;

-- Cập nhật NgayCapNhat cho tất cả các bản ghi điểm hiện tại
UPDATE Diem 
SET NgayCapNhat = CURRENT_TIMESTAMP 
WHERE MaLopHocPhan IS NOT NULL 
  AND MaSinhVien IS NOT NULL;

-- Thông báo hoàn thành
SELECT 'Đã cập nhật thành công thời gian cập nhật cho tất cả bản ghi điểm!' as ThongBao;
