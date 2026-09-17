<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Horizon Hotel</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="logo-container">
                <div class="logo-circle">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 21h18M5 21V6.5a1.5 1.5 0 0 1 1.06-.9l4.5-1.5a2 2 0 0 1 2.88 0l4.5 1.5A1.5 1.5 0 0 1 19 6.5V21M9 21v-6a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v6M10 9h4M10 13h4"
                            stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="12" cy="3" r="1" fill="#ffffff" />
                    </svg>
                </div>
            </div>

            <div class="header">
                <h1>Tạo Tài Khoản</h1>
                <div class="accent-line"></div>
                <p>Đăng ký để truy cập hệ thống quản lý</p>
            </div>

            <form action="index.php?route=register" method="POST" class="login-form">
                <?php if (!empty($error)): ?>
                    <p style="color: #e74c3c; font-size: 14px; text-align: center; margin-bottom: 2px; font-weight: 500;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <p style="color: #10b981; font-size: 14px; text-align: center; margin-bottom: 2px; font-weight: 500;"><?= htmlspecialchars($success) ?></p>
                <?php endif; ?>
                
                <div class="input-group">
                    <label for="fullname">Họ và tên</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" id="fullname" name="fullname" placeholder="Vd: Nguyễn Văn A" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock" style="font-size: 16px;"></i>
                        <input type="password" id="password" name="password" placeholder="Mật khẩu (tối thiểu 6 ký tự)" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Đăng ký ngay</button>
                
                <div class="register-link">
                    <p>Đã có tài khoản? <a href="index.php?route=login">Đăng nhập</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
