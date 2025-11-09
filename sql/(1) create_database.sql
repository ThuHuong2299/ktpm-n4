-- Create the database
CREATE DATABASE IF NOT EXISTS QuanLyDiem
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE QuanLyDiem;

-- Create LopHocPhan table
CREATE TABLE LopHocPhan (
    MaLopHocPhan VARCHAR(25) PRIMARY KEY,
    TenMonHoc VARCHAR(100) NOT NULL,
    HocKy TINYINT CHECK (HocKy IN (1, 2)),
    NamHoc CHAR(9),
    GiangVienPhuTrach VARCHAR(100) NOT NULL,
    TrangThaiLop VARCHAR(20) CHECK (TrangThaiLop IN ('hoạt động', 'đã xóa')),
    LyDoCapNhatXoa VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create SinhVien table
CREATE TABLE SinhVien (
    MaSinhVien VARCHAR(10) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    Email VARCHAR(100),
    NgaySinh DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create SinhVien_LopHocPhan table
CREATE TABLE SinhVien_LopHocPhan (
    MaLopHocPhan VARCHAR(25),
    MaSinhVien VARCHAR(10),
    NgayDangKy DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThaiDangKy VARCHAR(20) DEFAULT 'đang học' 
        CHECK (TrangThaiDangKy IN ('đang học', 'đã rút', 'hoàn thành')),
    LyDoThayDoi VARCHAR(255),
    PRIMARY KEY (MaLopHocPhan, MaSinhVien),
    FOREIGN KEY (MaLopHocPhan) REFERENCES LopHocPhan(MaLopHocPhan),
    FOREIGN KEY (MaSinhVien) REFERENCES SinhVien(MaSinhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Diem table
CREATE TABLE Diem (
    MaLopHocPhan VARCHAR(25),
    MaSinhVien VARCHAR(10),
    DiemChuyenCan DECIMAL(3,1) CHECK (DiemChuyenCan BETWEEN 0.0 AND 10.0),
    DiemGiuaKy DECIMAL(3,1) CHECK (DiemGiuaKy BETWEEN 0.0 AND 10.0),
    DiemCuoiKy DECIMAL(3,1) CHECK (DiemCuoiKy BETWEEN 0.0 AND 10.0),
    DiemTongKet DECIMAL(3,2) CHECK (DiemTongKet BETWEEN 0.0 AND 10.0),
    XepLoaiChu CHAR(2),
    TinhTrang VARCHAR(20),
    NgayCapNhat DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (MaLopHocPhan, MaSinhVien),
    FOREIGN KEY (MaLopHocPhan) REFERENCES LopHocPhan(MaLopHocPhan),
    FOREIGN KEY (MaSinhVien) REFERENCES SinhVien(MaSinhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create BaoCaoXepLoai table
CREATE TABLE BaoCaoXepLoai (
    MaBaoCao BIGINT AUTO_INCREMENT PRIMARY KEY,
    MaLopHocPhan VARCHAR(25),
    NgayInBaoCao DATETIME DEFAULT CURRENT_TIMESTAMP,
    NguoiThucHien VARCHAR(100) NOT NULL,
    DinhDangXuat VARCHAR(10) CHECK (DinhDangXuat IN ('PDF', 'Excel')),
    FOREIGN KEY (MaLopHocPhan) REFERENCES LopHocPhan(MaLopHocPhan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create trigger for automatic grade calculation
DELIMITER //
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
    
    SET NEW.NgayCapNhat = CURRENT_TIMESTAMP;
END;//
DELIMITER ;

-- Create trigger for updating grades
DELIMITER //
CREATE TRIGGER trg_CapNhatDiem
BEFORE UPDATE ON Diem
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
    
    SET NEW.NgayCapNhat = CURRENT_TIMESTAMP;
END;//
DELIMITER ;