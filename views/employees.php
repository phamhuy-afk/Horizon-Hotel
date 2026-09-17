<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhân Viên - Horizon Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <main class="main-content">
        <header class="page-header" style="display:flex; justify-content:space-between; align-items:flex-end;">
            <div>
                <h1>Nhân Viên</h1>
                <p>Quản lý đội ngũ nhân sự và quyền truy cập hệ thống</p>
            </div>
            <button class="btn-dark" onclick="openEModal('add')">
                <i class="fa-solid fa-plus"></i> Thêm Nhân Viên
            </button>
        </header>

        <!-- FLASH MESSAGES -->
        <?php if (isset($_SESSION['flash_msg'])): ?>
            <div style="background: <?= $_SESSION['flash_msg']['bg'] ?>; color: <?= $_SESSION['flash_msg']['color'] ?>; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between;">
                <span><?= $_SESSION['flash_msg']['text'] ?></span>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <?php unset($_SESSION['flash_msg']); ?>
        <?php endif; ?>

        <div class="booking-table-wrap">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ Tên</th>
                        <th>Email</th>
                        <th style="text-align:center;">Chức Vụ</th>
                        <th style="text-align:center;">Ngày Tạo</th>
                        <th style="text-align:center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($employees as $e): ?>
                <tr>
                    <td class="booking-id">#<?= $e['id'] ?></td>
                    <td class="guest-name"><?= htmlspecialchars($e['fullname']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td style="text-align:center;">
                        <?php if($e['role'] === 'admin'): ?>
                            <span class="cust-badge cust-vip" style="background:#fef3c7; color:#d97706;">Quản Trị Viên</span>
                        <?php elseif($e['role'] === 'staff'): ?>
                            <span class="cust-badge cust-normal" style="background:#e0f2fe; color:#0369a1;">Nhân Viên</span>
                        <?php else: ?>
                            <span class="cust-badge cust-normal" style="background:#f1f5f9; color:#64748b;">Khách Hàng</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center; color:#64748b; font-size:13px;"><?= date('d/m/Y', strtotime($e['created_at'])) ?></td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <button class="btn-action edit" onclick='openEModal("edit", <?= json_encode($e) ?>)' title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <?php if($e['id'] != $_SESSION['user_id']): ?>
                            <a href="index.php?route=employees&action=delete&id=<?= $e['id'] ?>" class="btn-action delete"
                               onclick="return confirm('Xóa nhân viên <?= htmlspecialchars($e['fullname']) ?>?')" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- MODAL THÊM / SỬA -->
    <div class="modal-overlay" id="eModal">
        <div class="modal-box" style="max-width:500px;">
            <button class="modal-close" onclick="closeEModal()"><i class="fa-solid fa-xmark"></i></button>
            <h2 class="modal-title" id="eModalTitle">Thêm Nhân Viên</h2>

            <form action="index.php?route=employees" method="POST" id="eForm">
                <input type="hidden" name="action" id="eAction" value="add">
                <input type="hidden" name="id" id="eId">

                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="fullname" id="eFullname" required>
                </div>

                <div class="form-group">
                    <label>Email (Tài khoản đăng nhập)</label>
                    <input type="email" name="email" id="eEmail" required>
                </div>

                <div class="form-group">
                    <label id="passLabel">Mật khẩu</label>
                    <input type="password" name="password" id="ePassword" placeholder="Để trống nếu không muốn đổi (khi sửa)">
                </div>

                <div class="form-group">
                    <label>Chức Vụ / Quyền Hạn</label>
                    <select name="role" id="eRole">
                        <option value="staff">Nhân Viên (Staff)</option>
                        <option value="admin">Quản Trị Viên (Admin)</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEModal()">Hủy</button>
                    <button type="submit" class="btn-dark">Lưu Thông Tin</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const eModal = document.getElementById('eModal');
        function openEModal(mode, data = null) {
            eModal.classList.add('active');
            if (mode === 'add') {
                document.getElementById('eModalTitle').innerText = 'Thêm Nhân Viên Mới';
                document.getElementById('eAction').value = 'add';
                document.getElementById('ePassword').required = true;
                document.getElementById('passLabel').innerText = 'Mật khẩu';
                document.getElementById('eForm').reset();
            } else {
                document.getElementById('eModalTitle').innerText = 'Chỉnh Sửa Nhân Viên';
                document.getElementById('eAction').value = 'edit';
                document.getElementById('ePassword').required = false;
                document.getElementById('passLabel').innerText = 'Đổi mật khẩu (tùy chọn)';
                document.getElementById('eId').value = data.id;
                document.getElementById('eFullname').value = data.fullname;
                document.getElementById('eEmail').value = data.email;
                document.getElementById('eRole').value = data.role;
            }
        }
        function closeEModal() { eModal.classList.remove('active'); }
        eModal.addEventListener('click', e => { if(e.target === eModal) closeEModal(); });
    </script>
</body>
</html>


