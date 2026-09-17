<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch Vụ - Horizon Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <main class="main-content">
        <header class="page-header" style="display:flex; justify-content:space-between; align-items:flex-end;">
            <div>
                <h1>Dịch Vụ</h1>
                <p>Quản lý các dịch vụ đi kèm của khách sạn</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="index.php?route=service_orders" class="btn-dark" style="background: #10b981; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-clipboard-list"></i> Đơn Dịch Vụ
                </a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <button class="btn-dark" onclick="openSModal('add')">
                    <i class="fa-solid fa-plus"></i> Thêm Dịch Vụ
                </button>
                <?php endif; ?>
            </div>
        </header>

        <div class="room-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            <?php foreach($services as $s): 
                $cat = $categories[$s['category']] ?? $categories['other'];
            ?>
            <div class="room-card" style="border-left: 4px solid <?= $cat['color'] ?>;">
                <div class="room-card-header">
                    <h3 style="font-size: 18px;"><?= htmlspecialchars($s['service_name']) ?></h3>
                    <span class="room-badge" style="background: <?= $cat['color'] ?>20; color: <?= $cat['color'] ?>; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                        <i class="fa-solid <?= $cat['icon'] ?>"></i> <?= $cat['text'] ?>
                    </span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin: 12px 0; min-height: 40px;"><?= htmlspecialchars($s['description'] ?: 'Chưa có mô tả.') ?></p>
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: auto;">
                    <div class="room-price" style="margin-top: 0">
                        <h3 style="color: #0f172a;"><?= number_format($s['price'], 0, ',', '.') ?> đ</h3>
                        <p style="font-size: 11px;"><?= $s['category'] === 'table' ? 'phí đặt bàn' : 'mỗi đơn vị' ?></p>
                    </div>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn-action edit" onclick="openSModal('edit', <?= htmlspecialchars(json_encode($s)) ?>)" title="Sửa">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <a href="index.php?route=services&action=delete&id=<?= $s['id'] ?>" class="btn-action delete" onclick="return confirm('Xóa dịch vụ này?')" title="Xóa">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if(empty($services)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 60px 0; color: #94a3b8;">
                    <i class="fa-solid fa-concierge-bell" style="font-size: 48px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p>Chưa có dịch vụ nào. Hãy thêm dịch vụ đầu tiên!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- MODAL -->
    <div class="modal-overlay" id="sModal">
        <div class="modal-box" style="max-width: 500px;">
            <button class="modal-close" onclick="closeSModal()"><i class="fa-solid fa-xmark"></i></button>
            <h2 class="modal-title" id="sModalTitle">Thêm Dịch Vụ Mới</h2>
            
            <form action="index.php?route=services" method="POST" id="sForm">
                <input type="hidden" name="action" id="sAction" value="add">
                <input type="hidden" name="id" id="sId" value="">

                <div class="form-group">
                    <label>Tên Dịch Vụ / Món Ăn</label>
                    <input type="text" name="service_name" id="sName" placeholder="VD: Pizza Hải Sản" required>
                </div>
                
                <div class="form-group">
                    <label>Mô Tả</label>
                    <textarea name="description" id="sDesc" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; resize: vertical;" rows="3" placeholder="Thành phần, ghi chú..."></textarea>
                </div>

                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label>Giá (VNĐ)</label>
                        <input type="number" name="price" id="sPrice" placeholder="VD: 50000" required>
                    </div>
                    <div>
                        <label>Phân Loại</label>
                        <select name="category" id="sCategory">
                            <option value="food">Đồ Ăn / Uống</option>
                            <option value="table">Đặt Bàn</option>
                            <option value="other">Dịch Vụ Khác</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Trạng Thái</label>
                    <select name="status" id="sStatus">
                        <option value="active">Đang Kinh Doanh</option>
                        <option value="inactive">Tạm Ngừng</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeSModal()">Hủy</button>
                    <button type="submit" class="btn-dark">Lưu Thông Tin</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const sModal = document.getElementById('sModal');
        function openSModal(mode, data = null) {
            sModal.classList.add('active');
            if(mode === 'add') {
                document.getElementById('sModalTitle').innerText = "Thêm Dịch Vụ";
                document.getElementById('sAction').value = 'add';
                document.getElementById('sForm').reset();
            } else if (mode === 'edit' && data) {
                document.getElementById('sModalTitle').innerText = "Cập Nhật Dịch Vụ";
                document.getElementById('sAction').value = 'edit';
                document.getElementById('sId').value = data.id;
                document.getElementById('sName').value = data.service_name;
                document.getElementById('sDesc').value = data.description;
                document.getElementById('sPrice').value = data.price;
                document.getElementById('sCategory').value = data.category;
                document.getElementById('sStatus').value = data.status;
            }
        }
        function closeSModal() { sModal.classList.remove('active'); }
        sModal.addEventListener('click', e => { if(e.target === sModal) closeSModal(); });
    </script>
</body>
</html>


