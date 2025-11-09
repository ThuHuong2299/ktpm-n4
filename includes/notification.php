<?php
/**
 * Hệ thống thông báo (Notification System)
 * File này chứa tất cả logic xử lý thông báo cho toàn bộ ứng dụng
 * Sử dụng: Chỉ cần include file này vào header.php là có thể sử dụng được
 */
?>

<!-- Container chứa các thông báo - hiển thị ở góc phải màn hình -->
<div id="notification-container"></div>

<script>
/**
 * Hệ thống thông báo JavaScript
 */

/**
 * Hiển thị thông báo với animation trượt từ phải sang trái
 * @param {string} message - Nội dung thông báo (hỗ trợ HTML)
 * @param {string} type - Loại thông báo: 'success', 'error', 'warning', 'info'
 * @param {number} duration - Thời gian hiển thị (milliseconds), mặc định 4000ms
 */
function showNotification(message, type = 'info', duration = 4000) {
    const container = document.getElementById('notification-container');
    
    // Tạo element thông báo
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = message;
    
    // Thêm event click để đóng thông báo
    notification.addEventListener('click', function() {
        hideNotification(notification);
    });
    
    // Thêm vào container
    container.appendChild(notification);
    
    // Animation hiển thị với delay nhỏ
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Tự động ẩn sau thời gian định sẵn
    setTimeout(() => {
        hideNotification(notification);
    }, duration);
}

/**
 * Ẩn thông báo với animation
 * @param {Element} notification - Element thông báo cần ẩn
 */
function hideNotification(notification) {
    if (notification && notification.parentNode) {
        // Animation trượt ra ngoài
        notification.style.transform = 'translateX(400px)';
        notification.style.opacity = '0';
        
        // Xóa element sau khi animation hoàn thành
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
}

/**
 * Các hàm tiện ích để hiển thị thông báo nhanh
 */
function showSuccess(message, duration = 4000) {
    showNotification(message, 'success', duration);
}

function showError(message, duration = 5000) {
    showNotification(message, 'error', duration);
}

function showWarning(message, duration = 4000) {
    showNotification(message, 'warning', duration);
}

function showInfo(message, duration = 4000) {
    showNotification(message, 'info', duration);
}

/**
 * Validation cho mã lớp học phần
 * Format: MMM_HHHHHHH_YY
 * - MMM: 3 chữ số (năm học + học kỳ) 
 * - HHHHHHH: 6-8 ký tự IN HOA hoặc số (mã môn học)
 * - YY: 2 chữ số (đuôi lớp)
 * 
 * @param {HTMLInputElement} input - Input element chứa mã lớp
 * @returns {boolean} - true nếu hợp lệ, false nếu không hợp lệ
 */
function validateClassCode(input) {
    const value = input.value.trim();
    
    // Kiểm tra rỗng
    if (!value) {
        showError('Vui lòng nhập mã lớp học phần');
        return false;
    }
    
    // Pattern validation: MMM_HHHHHHH_YY
    const pattern = /^[0-9]{3}_[A-Z0-9]{6,8}_[0-9]{2}$/;
    
    if (!pattern.test(value)) {
        showError(
            '<strong>Mã lớp không đúng định dạng!</strong><br>' +
            'Định dạng đúng: <strong>MMM_HHHHHHH_YY</strong><br>' +
            '• MMM: 3 chữ số (năm học + học kỳ)<br>' +
            '• HHHHHHH: 6-8 ký tự IN HOA hoặc số<br>' +
            '• YY: 2 chữ số (đuôi lớp)<br>' +
            '<em>Ví dụ: 251_EECIT3021_01</em>',
            6000 // Hiển thị lâu hơn vì thông báo dài
        );
        return false;
    }
    
    // Kiểm tra logic nghiệp vụ
    const parts = value.split('_');
    const yearSemester = parts[0];
    const courseCode = parts[1];
    const classNumber = parts[2];
    
    // Kiểm tra năm học (MMM)
    const year = parseInt(yearSemester.substring(0, 2)) + 2000;
    const semester = parseInt(yearSemester.substring(2, 3));
    
    if (semester < 1 || semester > 2) {
        showError('Học kỳ phải là 1 hoặc 2 (ký tự thứ 3 của mã lớp)');
        return false;
    }
    
    const currentYear = new Date().getFullYear();
    if (year < currentYear - 5 || year > currentYear + 2) {
        showWarning(`Năm học ${year} có vẻ không hợp lý. Vui lòng kiểm tra lại.`);
    }
    
    return true;
}

/**
 * Validation cho form tìm kiếm
 * @param {HTMLInputElement} input - Input tìm kiếm
 * @returns {boolean} - true nếu hợp lệ
 */
function validateSearchInput(input) {
    const value = input.value.trim();
    
    if (!value) {
        showWarning('Vui lòng nhập từ khóa tìm kiếm trước khi thực hiện tra cứu');
        input.focus(); // Focus vào input để user nhập
        return false;
    }
    
    if (value.length < 2) {
        showWarning('Từ khóa tìm kiếm phải có ít nhất 2 ký tự');
        return false;
    }
    
    return true;
}

/**
 * Thêm event listeners khi trang được load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Tự động validation cho các input có class 'validate-class-code'
    const classCodeInputs = document.querySelectorAll('.validate-class-code');
    classCodeInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            validateClassCode(this);
        });
        
        // Real-time validation khi gõ (với debounce)
        let timeout;
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if (this.value.trim()) {
                    validateClassCode(this);
                }
            }, 1000); // Delay 1 giây sau khi ngừng gõ
        });
    });
    
    // Auto validation cho search forms
    const searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput && !validateSearchInput(searchInput)) {
                e.preventDefault();
                return false;
            }
        });
    });
});

// Export functions để có thể sử dụng từ PHP
window.showNotification = showNotification;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
window.validateClassCode = validateClassCode;
window.validateSearchInput = validateSearchInput;

</script>