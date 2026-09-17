<?php
/**
 * views/layouts/sidebar.php
 * Shared sidebar navigation - dùng chung cho tất cả các trang Dashboard
 * 
 * Biến cần được truyền từ Controller:
 * - $current_route: Route hiện tại để highlight menu item đang active
 * - $fullname: Tên đăng nhập của người dùng hiện tại
 */
$current_route = $_GET['route'] ?? 'dashboard';
$session_fullname = $_SESSION['fullname'] ?? 'Admin';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="brand-icon"><i class="fa-solid fa-hotel"></i></div>
        <h2>Horizon<br>Hotel</h2>
    </div>

    <nav class="sidebar-nav">
        <a href="index.php?route=dashboard" class="nav-item <?= $current_route === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-border-all"></i><span>Tổng Quan</span>
        </a>
        <a href="index.php?route=rooms" class="nav-item <?= $current_route === 'rooms' ? 'active' : '' ?>">
            <i class="fa-solid fa-bed"></i><span>Phòng</span>
        </a>
        <a href="index.php?route=bookings" class="nav-item <?= $current_route === 'bookings' ? 'active' : '' ?>">
            <i class="fa-regular fa-calendar-check"></i><span>Đặt Phòng</span>
        </a>
        <a href="index.php?route=customers" class="nav-item <?= $current_route === 'customers' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-group"></i><span>Khách Hàng</span>
        </a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="index.php?route=employees" class="nav-item <?= $current_route === 'employees' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-tie"></i><span>Nhân Viên</span>
        </a>
        <?php endif; ?>
        <a href="index.php?route=services" class="nav-item <?= $current_route === 'services' ? 'active' : '' ?>">
            <i class="fa-solid fa-utensils"></i><span>Dịch Vụ</span>
        </a>
        <a href="index.php?route=service_orders" class="nav-item <?= $current_route === 'service_orders' ? 'active' : '' ?>">
            <i class="fa-solid fa-bell-concierge"></i><span>Gọi Dịch Vụ</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <p class="role-text" style="font-size:11px;">Đăng nhập bởi</p>
            <p class="email-text" style="font-size:14px; font-weight:600;">
                <?= htmlspecialchars($session_fullname) ?>
            </p>
        </div>
        <a href="index.php?route=logout" class="logout-btn">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
        </a>
    </div>
</aside>
