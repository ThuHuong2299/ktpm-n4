-- Cập nhật thời gian cho các bản ghi điểm hiện tại (không tính dữ liệu mẫu)
-- Script này sẽ cập nhật NgayCapNhat thành thời điểm hiện tại cho tất cả các bản ghi điểm có sẵn

-- Cập nhật NgayCapNhat cho tất cả các bản ghi điểm hiện tại
UPDATE Diem 
SET NgayCapNhat = CURRENT_TIMESTAMP 
WHERE MaLopHocPhan IS NOT NULL 
  AND MaSinhVien IS NOT NULL;

-- Tạo trigger để tự động cập nhật thời gian khi có thay đổi điểm
-- (Cập nhật lại trigger hiện tại để đảm bảo luôn cập nhật thời gian)

-- Xóa trigger cũ nếu tồn tại
DROP TRIGGER IF EXISTS trg_CapNhatDiem;

-- Tạo lại trigger cập nhật với logic mới
DELIMITER //
CREATE TRIGGER trg_CapNhatDiem
BEFORE UPDATE ON Diem
FOR EACH ROW
BEGIN
    -- Tính toán lại điểm tổng kết nếu có đầy đủ thông tin
    IF NEW.DiemChuyenCan IS NOT NULL AND NEW.DiemGiuaKy IS NOT NULL AND NEW.DiemCuoiKy IS NOT NULL THEN
        SET NEW.DiemTongKet = ROUND((NEW.DiemChuyenCan * 0.1 + NEW.DiemGiuaKy * 0.3 + NEW.DiemCuoiKy * 0.6), 2);
        
        SET NEW.XepLoaiChu = 
            CASE 
                WHEN NEW.DiemTongKet >= 8.5 THEN 'A'
                WHEN NEW.DiemTongKet >= 8.0 THEN 'B+'
                WHEN NEW.DiemTongKet >= 7.0 THEN 'B'
                WHEN NEW.DiemTongKet >= 6.5 THEN 'C+'
                WHEN NEW.DiemTongKet >= 5.5 THEN 'C'
                WHEN NEW.DiemTongKet >= 5.0 THEN 'D+'
                WHEN NEW.DiemTongKet >= 4.0 THEN 'D'
                ELSE 'F'
            END;
        
        SET NEW.TinhTrang = 
            CASE 
                WHEN NEW.DiemTongKet >= 4.0 THEN 'Đạt'
                ELSE 'Không đạt'
            END;
    END IF;
    
    -- Luôn cập nhật thời gian khi có bất kỳ thay đổi nào
    SET NEW.NgayCapNhat = CURRENT_TIMESTAMP;
END;//

-- Tạo thêm trigger cho INSERT để đảm bảo thời gian được cập nhật
DROP TRIGGER IF EXISTS trg_TinhDiemTongKet;

CREATE TRIGGER trg_TinhDiemTongKet
BEFORE INSERT ON Diem
FOR EACH ROW
BEGIN
    IF NEW.DiemChuyenCan IS NOT NULL AND NEW.DiemGiuaKy IS NOT NULL AND NEW.DiemCuoiKy IS NOT NULL THEN
        SET NEW.DiemTongKet = ROUND((NEW.DiemChuyenCan * 0.1 + NEW.DiemGiuaKy * 0.3 + NEW.DiemCuoiKy * 0.6), 2);
        
        SET NEW.XepLoaiChu = 
            CASE 
                WHEN NEW.DiemTongKet >= 8.5 THEN 'A'
                WHEN NEW.DiemTongKet >= 8.0 THEN 'B+'
                WHEN NEW.DiemTongKet >= 7.0 THEN 'B'
                WHEN NEW.DiemTongKet >= 6.5 THEN 'C+'
                WHEN NEW.DiemTongKet >= 5.5 THEN 'C'
                WHEN NEW.DiemTongKet >= 5.0 THEN 'D+'
                WHEN NEW.DiemTongKet >= 4.0 THEN 'D'
                ELSE 'F'
            END;
        
        SET NEW.TinhTrang = 
            CASE 
                WHEN NEW.DiemTongKet >= 4.0 THEN 'Đạt'
                ELSE 'Không đạt'
            END;
    END IF;
    
    -- Luôn cập nhật thời gian khi thêm mới
    SET NEW.NgayCapNhat = CURRENT_TIMESTAMP;
END;//
DELIMITER ;

-- Thông báo hoàn thành
SELECT 'Đã cập nhật thành công thời gian cập nhật cho tất cả bản ghi điểm!' as ThongBao;
