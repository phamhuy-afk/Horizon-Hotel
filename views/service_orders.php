<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Dịch Vụ - Horizon Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <main class="main-content">
        <header class="page-header" style="display:flex; justify-content:space-between; align-items:flex-end;">
            <div>
                <h1>Đơn Dịch Vụ</h1>
                <p>Danh sách các yêu cầu gọi món, dịch vụ từ phòng</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="index.php?route=services" class="btn-dark" style="background: #64748b; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-left"></i> Quản Lý Dịch Vụ
                </a>
                <button class="btn-dark" onclick="openOModal('add')">
                    <i class="fa-solid fa-plus"></i> Tạo Đơn Mới
                </button>
            </div>
        </header>

        <div class="booking-table-wrap">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Phòng</th>
                        <th>Mã Đặt Phòng</th>
                        <th>Dịch Vụ / Món</th>
                        <th>Số Lượng</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Thời Gian</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($orders)): ?>
                        <tr><td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">Chưa có đơn dịch vụ nào.</td></tr>
                    <?php else: ?>
                        <?php foreach($orders as $o): 
                            $badge = $status_badges[$o['status']] ?? $status_badges['pending'];
                        ?>
                        <tr>
                            <td class="booking-id">#<?= $o['id'] ?></td>
                            <td style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($o['room_name']) ?></td>
                            <td style="text-align: center; color: #64748b; font-size: 13px;"><?= $o['booking_id'] ? '#'.$o['booking_id'] : '—' ?></td>
                            <td>
                                <strong><?= htmlspecialchars($o['service_name']) ?></strong>
                                <?php if($o['note']): ?><br><small style="color:#94a3b8;">🎬 <?= htmlspecialchars($o['note']) ?></small><?php endif; ?>
                            </td>
                            <td style="text-align: center;"><?= $o['quantity'] ?></td>
                            <td class="price-col"><?= number_format($o['total_price'], 0, ',', '.') ?> đ</td>
                            <td><span class="booking-badge <?= $badge['class'] ?>"><?= $badge['text'] ?></span></td>
                            <td style="font-size: 13px;"><?= date('H:i d/m', strtotime($o['created_at'])) ?></td>
                            <td class="action-col">

                                <button class="btn-action edit" onclick="openOModal('edit', <?= htmlspecialchars(json_encode($o)) ?>)" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="index.php?route=service_orders&action=delete&id=<?= $o['id'] ?>" class="btn-action delete" onclick="return confirm('Xóa đơn này?')" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- MODAL -->
    <div class="modal-overlay" id="oModal">
        <div class="modal-box" style="max-width: 500px;">
            <button class="modal-close" onclick="closeOModal()"><i class="fa-solid fa-xmark"></i></button>
            <h2 class="modal-title" id="oModalTitle">Tạo Đơn Dịch Vụ</h2>
            
            <form action="index.php?route=service_orders" method="POST" id="oForm">
                <input type="hidden" name="action" id="oAction" value="add">
                <input type="hidden" name="id" id="oId" value="">

                <div class="form-group">
                    <label>Chọn Phòng</label>
                    <select name="room_id" id="oRoomId" required>
                        <?php foreach($rooms_list as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['room_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Chọn Dịch Vụ / Món Ăn</label>
                    <select name="service_id" id="oServiceId" required onchange="updatePriceHint()">
                        <?php foreach($services_list as $s): ?>
                            <option value="<?= $s['id'] ?>" data-price="<?= $s['price'] ?>"><?= htmlspecialchars($s['service_name']) ?> - <?= number_format($s['price'], 0, ',', '.') ?>đ</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label>Số Lượng</label>
                        <input type="number" name="quantity" id="oQty" value="1" min="1" required onchange="updatePriceHint()">
                    </div>
                    <div>
                        <label>Trạng Thái</label>
                        <select name="status" id="oStatus">
                            <option value="pending">Chờ Xử Lý</option>
                            <option value="processing">Đang Chuẩn Bị</option>
                            <option value="completed">Đã Hoàn Thành</option>
                            <option value="cancelled">Đã Hủy</option>
                        </select>
                    </div>
                </div>

                <div id="priceHint" style="padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 20px; font-size: 14px; color: #1e293b;">
                    Tổng tiền dự tính: <strong id="hintTotal">0 đ</strong>
                </div>

                <div class="form-group">
                    <label>Ghi Chú (Yêu cầu thêm)</label>
                    <input type="text" name="note" id="oNote" placeholder="VD: Không lấy hành, mang thêm muỗng...">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeOModal()">Hủy</button>
                    <button type="submit" class="btn-dark">Xác Nhận Đơn</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const oModal = document.getElementById('oModal');
        function openOModal(mode, data = null) {
            oModal.classList.add('active');
            if(mode === 'add') {
                document.getElementById('oModalTitle').innerText = "Tạo Đơn Mới";
                document.getElementById('oAction').value = 'add';
                document.getElementById('oForm').reset();
            } else if (mode === 'edit' && data) {
                document.getElementById('oModalTitle').innerText = "Cập Nhật Đơn";
                document.getElementById('oAction').value = 'edit';
                document.getElementById('oId').value = data.id;
                document.getElementById('oRoomId').value = data.room_id;
                document.getElementById('oServiceId').value = data.service_id;
                document.getElementById('oQty').value = data.quantity;
                document.getElementById('oStatus').value = data.status;
                document.getElementById('oNote').value = data.note;
            }
            updatePriceHint();
        }
        function closeOModal() { oModal.classList.remove('active'); }
        oModal.addEventListener('click', e => { if(e.target === oModal) closeOModal(); });

        function updatePriceHint() {
            const sEl = document.getElementById('oServiceId');
            const qty = parseInt(document.getElementById('oQty').value) || 0;
            const opt = sEl.options[sEl.selectedIndex];
            const price = opt ? parseFloat(opt.dataset.price) : 0;
            const total = price * qty;
            document.getElementById('hintTotal').innerText = total.toLocaleString('vi-VN') + ' đ';
        }

        // Prevent double submit
        const oForm = document.getElementById('oForm');
        if (oForm) {
            oForm.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerText = 'Đang xử lý...';
                    btn.style.opacity = '0.7';
                }
            });
        }
    </script>
</body>
</html>


