<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Phòng - Horizon Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<?php 
$badges = [
    'available'   => ['class' => 'badge-available',   'text' => 'Còn Trống'],
    'rented'      => ['class' => 'badge-rented',      'text' => 'Đang Thuê'],
    'maintenance' => ['class' => 'badge-maintenance', 'text' => 'Bảo Trì']
];
include __DIR__ . '/layouts/sidebar.php'; 
?>

    <main class="main-content">
        <header class="page-header" style="display:flex; justify-content:space-between; align-items:flex-end;">
            <div>
                <h1>Phòng</h1>
                <p>Quản lý danh sách phòng và trạng thái</p>
            </div>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <button class="btn-dark" onclick="openRoomModal('add')">
                <i class="fa-solid fa-plus"></i> Thêm Phòng
            </button>
            <?php endif; ?>
        </header>

        <div class="toolbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="liveSearch" placeholder="Tìm tên phòng, loại phòng..." autocomplete="off">
            </div>
            <div class="filter-box">
                <select id="statusFilter">
                    <option value="">Tất Cả Trạng Thái</option>
                    <option value="available">Còn Trống</option>
                    <option value="rented">Đang Thuê</option>
                    <option value="maintenance">Bảo Trì</option>
                </select>
            </div>
        </div>

        <div class="room-grid">
            <?php foreach($rooms as $r):
                $status_info = $badges[$r['status']] ?? $badges['available'];
                $ams = json_decode($r['amenities'], true);
                if (!is_array($ams)) $ams = [];
            ?>
            <div class="room-card">
                <div class="room-card-header">
                    <h3><?= htmlspecialchars($r['room_name']) ?></h3>
                    <span class="room-badge <?= $status_info['class'] ?>"><?= $status_info['text'] ?></span>
                </div>
                <p class="room-type"><?= htmlspecialchars($r['room_type']) ?></p>
                <div class="room-capacity">
                    <i class="fa-solid fa-bed"></i> <?= $r['capacity'] ?> Khách
                </div>
                <div class="room-amenities">
                    <?php if (empty($ams)): ?>
                        <span style="font-size:13px; color:#94a3b8;">Không có tiện ích nổi bật.</span>
                    <?php else: ?>
                        <?php foreach($ams as $icon): ?>
                        <div class="amenity-icon"><i class="fa-solid fa-<?= htmlspecialchars($icon) ?>"></i></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-top:auto;">
                    <div class="room-price" style="margin-top:0">
                        <h3 style="color:#0f172a; margin-bottom:0;"><?= number_format($r['price'], 0, ',', '.') ?> đ</h3>
                        <p style="font-size:11px; margin-top:0;">đêm / phòng</p>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button class="btn-action edit" onclick="openRoomModal('edit', <?= htmlspecialchars(json_encode($r)) ?>)" title="Sửa">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="index.php?route=rooms&action=delete&id=<?= $r['id'] ?>" class="btn-action delete"
                           onclick="return confirm('Xóa phòng này?')" title="Xóa">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if(empty($rooms)): ?>
                <p style="color:#64748b;">Chưa có phòng nào trong CSDL của bạn.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- MODAL THÊM / SỬA PHÒNG -->
    <div class="modal-overlay" id="roomModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            <h2 class="modal-title" id="modalTitle">Thêm Phòng Mới</h2>
            
            <form action="index.php?route=rooms" method="POST" id="roomForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="roomId" value="">

                <div class="form-group">
                    <label>Tên / Số Phòng</label>
                    <input type="text" name="room_name" id="room_name" placeholder="VD: Phòng 101" required>
                </div>
                
                <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label>Loại Phòng</label>
                        <select name="room_type" id="room_type">
                            <option value="Standard">Standard</option>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Suite">Suite</option>
                        </select>
                    </div>
                    <div>
                        <label>Sức chứa (Người)</label>
                        <input type="number" name="capacity" id="capacity" value="2" min="1" required>
                    </div>
                </div>

                <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label>Giá (VNĐ / Đêm)</label>
                        <input type="number" name="price" id="price" placeholder="VD: 150000" required>
                    </div>
                    <div>
                        <label>Trạng Thái</label>
                        <select name="status" id="status">
                            <option value="available">Còn Trống</option>
                            <option value="rented">Đang Thuê</option>
                            <option value="maintenance">Bảo Trì</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tiện ích có sẵn</label>
                    <div class="amenities-group">
                        <label><input type="checkbox" name="amenities[]" value="wifi"> Wifi</label>
                        <label><input type="checkbox" name="amenities[]" value="tv"> TV</label>
                        <label><input type="checkbox" name="amenities[]" value="mug-hot"> Trà/Café</label>
                        <label><input type="checkbox" name="amenities[]" value="bath"> Bồn tắm</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn-dark">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('roomModal');
        const formAction = document.getElementById('formAction');
        const modalTitle = document.getElementById('modalTitle');
        const roomId = document.getElementById('roomId');
        const isStaff = '<?= $_SESSION['role'] ?>' === 'staff';
        
        function openRoomModal(mode, roomData = null) {
            modal.classList.add('active');
            if(mode === 'add') {
                modalTitle.innerText = "Thêm Phòng Mới";
                formAction.value = 'add';
                document.getElementById('roomForm').reset();
            } else if (mode === 'edit' && roomData) {
                modalTitle.innerText = "Chỉnh Sửa Phòng";
                formAction.value = 'edit';
                roomId.value = roomData.id;
                document.getElementById('room_name').value = roomData.room_name;
                document.getElementById('room_type').value = roomData.room_type;
                document.getElementById('capacity').value = roomData.capacity;
                document.getElementById('price').value = roomData.price;
                document.getElementById('status').value = roomData.status;

                let ams = [];
                try { ams = JSON.parse(roomData.amenities) || []; } catch(e) {}
                const checkboxes = document.querySelectorAll('input[name="amenities[]"]');
                checkboxes.forEach(cb => { cb.checked = ams.includes(cb.value); });

                if (isStaff) {
                    document.getElementById('room_name').disabled = true;
                    document.getElementById('room_type').disabled = true;
                    document.getElementById('capacity').disabled = true;
                    document.getElementById('price').disabled = true;
                    checkboxes.forEach(cb => cb.disabled = true);
                } else {
                    document.getElementById('room_name').disabled = false;
                    document.getElementById('room_type').disabled = false;
                    document.getElementById('capacity').disabled = false;
                    document.getElementById('price').disabled = false;
                    checkboxes.forEach(cb => cb.disabled = false);
                }
            }
        }
        function closeModal() { modal.classList.remove('active'); }
        modal.addEventListener('click', e => { if(e.target === modal) closeModal(); });

        const searchInput = document.getElementById('liveSearch');
        const statusFilter = document.getElementById('statusFilter');
        const allCards = document.querySelectorAll('.room-card');

        function filterRooms() {
            const keyword = searchInput.value.toLowerCase().trim();
            const statusVal = statusFilter.value;
            let visibleCount = 0;
            allCards.forEach(card => {
                const name = card.querySelector('.room-card-header h3')?.innerText.toLowerCase() || '';
                const type = card.querySelector('.room-type')?.innerText.toLowerCase() || '';
                const badgeClass = card.querySelector('.room-badge')?.className || '';
                const matchSearch = keyword === '' || name.includes(keyword) || type.includes(keyword);
                let matchStatus = true;
                if (statusVal === 'available') matchStatus = badgeClass.includes('badge-available');
                else if (statusVal === 'rented') matchStatus = badgeClass.includes('badge-rented');
                else if (statusVal === 'maintenance') matchStatus = badgeClass.includes('badge-maintenance');
                if (matchSearch && matchStatus) { card.style.display = ''; visibleCount++; }
                else { card.style.display = 'none'; }
            });
        }
        searchInput.addEventListener('input', filterRooms);
        statusFilter.addEventListener('change', filterRooms);
    </script>
</body>
</html>
