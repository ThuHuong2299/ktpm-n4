-- Cập nhật cấu trúc bảng Diem và công thức tính điểm theo quy định TMU
-- Chạy file này sau khi đã import dữ liệu mẫu và chạy file bổ sung SoTinChi

USE QuanLyDiem;

-- Thêm các cột mới vào bảng Diem cho hệ thống chấm điểm TMU
ALTER TABLE Diem 
ADD COLUMN DiemGiuaKy2 DECIMAL(3,1) DEFAULT NULL AFTER DiemGiuaKy,
ADD COLUMN DiemThaoLuan DECIMAL(3,1) DEFAULT NULL AFTER DiemGiuaKy2;

-- Xóa các trigger cũ nếu tồn tại
DROP TRIGGER IF EXISTS trg_TinhDiemTongKet;
DROP TRIGGER IF EXISTS trg_CapNhatDiem;
DROP TRIGGER IF EXISTS trg_TinhDiemTongKet_TMU;
DROP TRIGGER IF EXISTS trg_CapNhatDiem_TMU;

-- Tạo trigger mới để tính điểm theo công thức TMU khi thêm dữ liệu
DELIMITER //
CREATE TRIGGER trg_TinhDiem_TMU_INSERT
BEFORE INSERT ON Diem
FOR EACH ROW
BEGIN
    DECLARE so_tin_chi INT DEFAULT 3;
    
    -- Lấy số tín chỉ của môn học
    SELECT SoTinChi INTO so_tin_chi 
    FROM LopHocPhan 
    WHERE MaLopHocPhan = NEW.MaLopHocPhan;
    
    -- Tính điểm tổng kết nếu có đủ điểm chuyên cần và cuối kỳ
    IF NEW.DiemChuyenCan IS NOT NULL AND NEW.DiemCuoiKy IS NOT NULL THEN
        
        IF so_tin_chi = 2 THEN
            -- Công thức cho môn 2 tín chỉ: (CC × 0.1) + (GK × 0.15) + (TL × 0.15) + (CK × 0.6)
            SET NEW.DiemTongKet = ROUND(
                (IFNULL(NEW.DiemChuyenCan, 0) * 0.1) + 
                (IFNULL(NEW.DiemGiuaKy, 0) * 0.15) + 
                (IFNULL(NEW.DiemThaoLuan, 0) * 0.15) + 
                (IFNULL(NEW.DiemCuoiKy, 0) * 0.6), 2
            );
            
        ELSE
            -- Công thức cho môn 3+ tín chỉ: (CC × 0.1) + {(GK1 + GK2)/2 × 0.15} + (TL × 0.15) + (CK × 0.6)
            SET NEW.DiemTongKet = ROUND(
                (IFNULL(NEW.DiemChuyenCan, 0) * 0.1) + 
                (((IFNULL(NEW.DiemGiuaKy, 0) + IFNULL(NEW.DiemGiuaKy2, NEW.DiemGiuaKy)) / 2) * 0.15) + 
                (IFNULL(NEW.DiemThaoLuan, 0) * 0.15) + 
                (IFNULL(NEW.DiemCuoiKy, 0) * 0.6), 2
            );
        END IF;
        
        -- Xác định xếp loại chữ theo thang điểm TMU
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
        
        -- Xác định tình trạng đậu/rớt
        SET NEW.TinhTrang = 
            CASE 
                WHEN NEW.DiemTongKet >= 4.0 THEN 'Đạt'
                ELSE 'Không đạt'
            END;
    END IF;
    
    -- Cập nhật thời gian
    SET NEW.NgayCapNhat = NOW();
END;//
DELIMITER ;

-- Tạo trigger mới để tính điểm theo công thức TMU khi cập nhật dữ liệu
DELIMITER //
CREATE TRIGGER trg_TinhDiem_TMU_UPDATE
BEFORE UPDATE ON Diem
FOR EACH ROW
BEGIN
    DECLARE so_tin_chi INT DEFAULT 3;
    
    -- Lấy số tín chỉ của môn học
    SELECT SoTinChi INTO so_tin_chi 
    FROM LopHocPhan 
    WHERE MaLopHocPhan = NEW.MaLopHocPhan;
    
    -- Tính điểm tổng kết nếu có đủ điểm chuyên cần và cuối kỳ
    IF NEW.DiemChuyenCan IS NOT NULL AND NEW.DiemCuoiKy IS NOT NULL THEN
        
        IF so_tin_chi = 2 THEN
            -- Công thức cho môn 2 tín chỉ: (CC × 0.1) + (GK × 0.15) + (TL × 0.15) + (CK × 0.6)
            SET NEW.DiemTongKet = ROUND(
                (IFNULL(NEW.DiemChuyenCan, 0) * 0.1) + 
                (IFNULL(NEW.DiemGiuaKy, 0) * 0.15) + 
                (IFNULL(NEW.DiemThaoLuan, 0) * 0.15) + 
                (IFNULL(NEW.DiemCuoiKy, 0) * 0.6), 2
            );
            
        ELSE
            -- Công thức cho môn 3+ tín chỉ: (CC × 0.1) + {(GK1 + GK2)/2 × 0.15} + (TL × 0.15) + (CK × 0.6)
            SET NEW.DiemTongKet = ROUND(
                (IFNULL(NEW.DiemChuyenCan, 0) * 0.1) + 
                (((IFNULL(NEW.DiemGiuaKy, 0) + IFNULL(NEW.DiemGiuaKy2, NEW.DiemGiuaKy)) / 2) * 0.15) + 
                (IFNULL(NEW.DiemThaoLuan, 0) * 0.15) + 
                (IFNULL(NEW.DiemCuoiKy, 0) * 0.6), 2
            );
        END IF;
        
        -- Xác định xếp loại chữ theo thang điểm TMU
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
        
        -- Xác định tình trạng đậu/rớt
        SET NEW.TinhTrang = 
            CASE 
                WHEN NEW.DiemTongKet >= 4.0 THEN 'Đạt'
                ELSE 'Không đạt'
            END;
    END IF;
    
    -- Cập nhật thời gian
    SET NEW.NgayCapNhat = NOW();
END;//
DELIMITER ;

-- Cập nhật dữ liệu mẫu với điểm thảo luận và điểm giữa kỳ 2

-- Cập nhật điểm thảo luận cho môn Tiếng Anh (2 tín chỉ)
UPDATE Diem 
SET DiemThaoLuan = 8.0
WHERE MaLopHocPhan = '251_ENGL1015_01';

-- Cập nhật điểm giữa kỳ 2 và điểm thảo luận cho các môn 3+ tín chỉ
UPDATE Diem 
SET DiemGiuaKy2 = ROUND(DiemGiuaKy + (RAND() * 2 - 1), 1),
    DiemThaoLuan = ROUND(7.0 + (RAND() * 3), 1)
WHERE MaLopHocPhan NOT LIKE '%ENGL%' AND DiemGiuaKy IS NOT NULL;

-- Đảm bảo điểm nằm trong khoảng hợp lệ (0-10)
UPDATE Diem 
SET DiemGiuaKy2 = CASE 
    WHEN DiemGiuaKy2 < 0 THEN 0
    WHEN DiemGiuaKy2 > 10 THEN 10
    ELSE DiemGiuaKy2
END,
DiemThaoLuan = CASE 
    WHEN DiemThaoLuan < 0 THEN 0
    WHEN DiemThaoLuan > 10 THEN 10
    ELSE DiemThaoLuan
END
WHERE DiemGiuaKy2 IS NOT NULL OR DiemThaoLuan IS NOT NULL;

-- Tính lại điểm tổng kết cho các bản ghi đã có điểm cuối kỳ
UPDATE Diem d
JOIN LopHocPhan l ON d.MaLopHocPhan = l.MaLopHocPhan
SET 
    d.DiemTongKet = CASE 
        WHEN l.SoTinChi = 2 THEN
            ROUND((IFNULL(d.DiemChuyenCan, 0) * 0.1) + (IFNULL(d.DiemGiuaKy, 0) * 0.15) + (IFNULL(d.DiemThaoLuan, 0) * 0.15) + (IFNULL(d.DiemCuoiKy, 0) * 0.6), 2)
        ELSE
            ROUND((IFNULL(d.DiemChuyenCan, 0) * 0.1) + (((IFNULL(d.DiemGiuaKy, 0) + IFNULL(d.DiemGiuaKy2, d.DiemGiuaKy)) / 2) * 0.15) + (IFNULL(d.DiemThaoLuan, 0) * 0.15) + (IFNULL(d.DiemCuoiKy, 0) * 0.6), 2)
    END,
    d.XepLoaiChu = CASE 
        WHEN d.DiemTongKet >= 8.5 THEN 'A'
        WHEN d.DiemTongKet >= 8.0 THEN 'B+'
        WHEN d.DiemTongKet >= 7.0 THEN 'B'
        WHEN d.DiemTongKet >= 6.5 THEN 'C+'
        WHEN d.DiemTongKet >= 5.5 THEN 'C'
        WHEN d.DiemTongKet >= 5.0 THEN 'D+'
        WHEN d.DiemTongKet >= 4.0 THEN 'D'
        ELSE 'F'
    END,
    d.TinhTrang = CASE 
        WHEN d.DiemTongKet >= 4.0 THEN 'Đạt'
        ELSE 'Không đạt'
    END,
    d.NgayCapNhat = NOW()
WHERE d.DiemCuoiKy IS NOT NULL;

-- Hiển thị kết quả sau khi cập nhật
SELECT 
    l.MaLopHocPhan,
    l.TenMonHoc,
    l.SoTinChi,
    COUNT(d.MaSinhVien) as SoLuongSinhVien,
    ROUND(AVG(d.DiemTongKet), 2) as DiemTrungBinh,
    SUM(CASE WHEN d.TinhTrang = 'Đạt' THEN 1 ELSE 0 END) as SoLuongDat,
    ROUND(SUM(CASE WHEN d.TinhTrang = 'Đạt' THEN 1 ELSE 0 END) * 100.0 / COUNT(d.MaSinhVien), 2) as TyLeDat
FROM LopHocPhan l
LEFT JOIN Diem d ON l.MaLopHocPhan = d.MaLopHocPhan
WHERE d.MaSinhVien IS NOT NULL
GROUP BY l.MaLopHocPhan, l.TenMonHoc, l.SoTinChi
ORDER BY l.MaLopHocPhan;

-- Hiển thị mẫu dữ liệu để kiểm tra
SELECT 
    d.MaLopHocPhan,
    l.TenMonHoc,
    l.SoTinChi,
    d.MaSinhVien,
    s.HoTen,
    d.DiemChuyenCan,
    d.DiemGiuaKy,
    d.DiemGiuaKy2,
    d.DiemThaoLuan,
    d.DiemCuoiKy,
    d.DiemTongKet,
    d.XepLoaiChu,
    d.TinhTrang
FROM Diem d
JOIN LopHocPhan l ON d.MaLopHocPhan = l.MaLopHocPhan
JOIN SinhVien s ON d.MaSinhVien = s.MaSinhVien
WHERE d.DiemTongKet IS NOT NULL
ORDER BY d.MaLopHocPhan, d.DiemTongKet DESC
LIMIT 10;
