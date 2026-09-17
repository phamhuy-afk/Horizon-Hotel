<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục & Thay đổi mật khẩu - Horizon Hotel</title>
    <link rel="stylesheet" href="css/login.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="logo-container">
                <div class="logo-circle">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3 21h18M5 21V6.5a1.5 1.5 0 0 1 1.06-.9l4.5-1.5a2 2 0 0 1 2.88 0l4.5 1.5A1.5 1.5 0 0 1 19 6.5V21M9 21v-6a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v6M10 9h4M10 13h4"
                            stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="12" cy="3" r="1" fill="#ffffff" />
                    </svg>
                </div>
            </div>

            <!-- Bước 1: Nhập Email -->
            <?php if ($step === 1): ?>
            <div class="header">
                <h1>Khôi Phục Mật Khẩu</h1>
                <div class="accent-line"></div>
                <p>Nhập email để xác thực tài khoản của bạn</p>
            </div>

            <form action="index.php?route=forgot-password" method="POST" class="login-form">
                <?php if (!empty($error) && $step === 1): ?>
                    <p style="color: #e74c3c; font-size: 14px; text-align: center; margin-bottom: 12px; font-weight: 500;">
                        <i class="fa-solid fa-circle-exclamation" style="margin-right:4px;"></i> <?= $error ?>
                    </p>
                <?php endif; ?>
                
                <input type="hidden" name="step" value="2">
                <div class="input-group" style="margin-bottom: 8px;">
                    <label for="email">Email đã đăng ký</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email" required
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                            oninvalid="this.setCustomValidity('Vui lòng nhập định dạng email hợp lệ')"
                            oninput="this.setCustomValidity('')">
                    </div>
                </div>

                <button type="submit" class="submit-btn" style="margin-top: 20px;">Gửi yêu cầu xác thực</button>
                
                <div class="register-link" style="margin-top: 20px;">
                    <p><a href="index.php?route=login"><i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Quay lại Đăng nhập</a></p>
                </div>
            </form>

            <!-- Bước 2: Đặt Mật Khẩu Mới -->
            <?php
elseif ($step === 2): ?>
            <div class="header">
                <h1>Tạo Mật Khẩu Mới</h1>
                <div class="accent-line"></div>
                <p>Vui lòng thiết lập mật khẩu mới cho tài khoản</p>
            </div>

            <form action="index.php?route=forgot-password" method="POST" class="login-form">
                <?php if (!empty($error) && $step === 2): ?>
                    <p style="color: #e74c3c; font-size: 14px; text-align: center; margin-bottom: 12px; font-weight: 500;">
                        <i class="fa-solid fa-circle-exclamation" style="margin-right:4px;"></i> <?= $error ?>
                    </p>
                <?php endif; ?>
                
                <input type="hidden" name="step" value="3">
                
                <div class="input-group" style="margin-bottom: 12px;">
                    <label for="new_password">Mật khẩu mới</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock" style="font-size: 16px;"></i>
                        <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" required minlength="6"
                            oninvalid="this.setCustomValidity('Vui lòng nhập mật khẩu (tối thiểu 6 ký tự)')"
                            oninput="this.setCustomValidity('')">
                    </div>
                </div>
                
                <div class="input-group" style="margin-bottom: 8px;">
                    <label for="confirm_password">Xác nhận mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock" style="font-size: 16px;"></i>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required minlength="6">
                    </div>
                </div>

                <button type="submit" class="submit-btn" style="margin-top: 20px;">Lưu mật khẩu mới</button>
                
                <div class="register-link" style="margin-top: 20px;">
                    <p><a href="index.php?route=login">Hủy và quay lại</a></p>
                </div>
            </form>
            
            <?php
elseif ($step === 3): ?>
            <div class="header">
                <h1 style="color: #10b981;">Thành Công!</h1>
                <div class="accent-line" style="background: linear-gradient(90deg, #10b981, #34d399, #10b981);"></div>
                <p style="color: #334155; margin-top: 10px;">Mật khẩu của bạn đã được thay đổi an toàn.</p>
            </div>
            
            <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-check" style="font-size: 64px; color: #10b981; margin-bottom: 30px; text-shadow: 0 4px 10px rgba(16,185,129,0.3);"></i>
                <a href="index.php?route=login" class="submit-btn" style="display: block; text-decoration: none; text-align: center;">Về màn hình Đăng nhập ngay</a>
            </div>
            <?php
endif; ?>

        </div>
    </div>
</body>

</html>


