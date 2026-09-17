<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Phòng - Horizon Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="page-header" style="display:flex; justify-content:space-between; align-items:flex-end;">
            <div>
                <h1>Đặt Phòng</h1>
                <p>Quản lý đặt phòng và nhận phòng</p>
            </div>
            <div style="display:flex; gap:10px;">
                <button id="bulkDeleteBtn" style="display:none; background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:8px; font-weight:600; cursor:pointer; align-items:center; justify-content:center; gap:8px;" onclick="bulkDelete()">
                    <i class="fa-solid fa-trash-can"></i> Xóa Đã Chọn (<span id="selectedCount">0</span>)
                </button>
                <button class="btn-dark" onclick="openBookingModal('add')">
                    <i class="fa-solid fa-plus"></i> Đặt Phòng Mới
                </button>
            </div>
        </header>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="liveSearch" placeholder="Tìm kiếm đặt phòng..." autocomplete="off">
            </div>
            <div class="filter-box">
                <select id="statusFilter">
                    <option value="">Tất Cả Trạng Thái</option>
                    <option value="checked_in">Đã Nhận Phòng</option>
                    <option value="confirmed">Đã Xác Nhận</option>
                    <option value="pending">Chờ Xác Nhận</option>
                    <option value="cancelled">Đã Hủy</option>
                    <option value="checked_out">Đã Trả Phòng</option>
                </select>
            </div>
        </div>

        <!-- TABLE -->
        <div class="booking-table-wrap">
            <table class="booking-table" id="bookingTable">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align:center;">
                            <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                        </th>
                        <th style="width: 80px;">Mã Đặt Phòng</th>
                        <th>Tên Khách</th>
                        <th>Số ĐT</th>
                        <th>Phòng</th>
                        <th>Ngày Nhận</th>
                        <th>Ngày Trả</th>
                        <th style="width: 60px; text-align:center;">Khách</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th style="min-width: 120px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($bookings)): ?>
                    <tr><td colspan="11" style="text-align:center; color:#94a3b8; padding:40px;">Chưa có đặt phòng nào.</td></tr>
                <?php else: ?>
                <?php foreach($bookings as $b):
                    $badge = $badge_map[$b['status']] ?? $badge_map['pending'];
                    $ci = date('d/m/Y', strtotime($b['check_in']));
                    $co = date('d/m/Y', strtotime($b['check_out']));
                ?>
                <tr data-name="<?= strtolower(htmlspecialchars($b['guest_name'])) ?>"
                    data-room="<?= strtolower(htmlspecialchars($b['room_name'])) ?>"
                    data-status="<?= $b['status'] ?>">
                    <td style="text-align:center;">
                        <input type="checkbox" class="booking-checkbox" value="<?= $b['id'] ?>" onclick="updateBulkBtn()">
                    </td>
                    <td class="booking-id">#<?= $b['id'] ?></td>
                    <td class="guest-name">
                        <?php if(!empty($b['customer_id'])): ?>
                            <a href="index.php?route=customers" style="color:#3b82f6; font-weight:600; text-decoration:none;" title="Xem khách hàng">
                                <?= htmlspecialchars($b['display_name']) ?>
                            </a>
                        <?php else: ?>
                            <?= htmlspecialchars($b['display_name']) ?>
                        <?php endif; ?>
                    </td>
                    <td style="color:#64748b; font-size:13px;">
                        <?php if(!empty($b['display_phone'])): ?>
                            <?= htmlspecialchars($b['display_phone']) ?>
                        <?php else: ?>
                            <span style="color:#cbd5e1;">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="room-link"><?= htmlspecialchars($b['room_name']) ?></td>
                    <td><?= $ci ?></td>
                    <td><?= $co ?></td>
                    <td style="text-align:center;"><?= $b['guests'] ?></td>
                    <td class="price-col"><?= number_format($b['total_price'], 0, ',', '.') ?> đ</td>
                    <td>
                        <span class="booking-badge <?= $badge['class'] ?>"><?= $badge['text'] ?></span>
                        <?php if (($b['payment_status'] ?? 'unpaid') === 'paid'): ?>
                            <span style="font-size: 11px; color:#10b981; font-weight:600;"><i class="fa-solid fa-circle-check"></i> Đã TT</span>
                        <?php endif; ?>
                    </td>
                    <td class="action-col">
                        <button class="btn-action edit" onclick="openBookingModal('edit', <?= htmlspecialchars(json_encode($b)) ?>)" title="Sửa">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <a href="index.php?route=bookings&action=delete&id=<?= $b['id'] ?>" class="btn-action delete"
                           onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đơn đặt phòng này?')" title="Xóa Đơn">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                        <?php if ($b['status'] === 'checked_in' || $b['status'] === 'checked_out'): ?>
                            <a href="index.php?route=invoice&id=<?= $b['id'] ?>" target="_blank" class="btn-action" style="background:#fef3c7; color:#d97706; text-decoration:none;" title="Xuất Hóa Đơn">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-box" style="max-width:580px; width:95%; max-height:92vh; display:flex; flex-direction:column; overflow:hidden; padding:0;">
            <button class="modal-close" onclick="closeBookingModal()" style="position:sticky; top:16px; right:24px; float:right; z-index:10;"><i class="fa-solid fa-xmark"></i></button>
            <h2 class="modal-title" id="bookingModalTitle" style="padding:32px 32px 0 32px; margin-bottom:0;">Đặt Phòng Mới</h2>

            <form action="index.php?route=bookings" method="POST" id="bookingForm" style="overflow-y:auto; padding:24px 32px 32px; flex:1;">
                <input type="hidden" name="action" id="bFormAction" value="add">
                <input type="hidden" name="id" id="bId">

                <input type="hidden" name="customer_id" id="bCustomerId" value="">

                <div class="form-group">
                    <label>Chọn Khách Hàng</label>
                    <select id="bCustomerSelect" onchange="selectCustomer(this)">
                        <option value="">-- Chọn khách hàng cũ --</option>
                        <?php foreach($customers_list as $c): ?>
                        <option value="<?= $c['id'] ?>" 
                            data-name="<?= htmlspecialchars($c['fullname']) ?>"
                            data-phone="<?= htmlspecialchars($c['phone'] ?? '') ?>">
                            <?= htmlspecialchars($c['fullname']) ?><?= $c['phone'] ? ' — '.$c['phone'] : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label>Tên Khách Hàng</label>
                        <input type="text" name="guest_name" id="bGuestName" placeholder="Nhập tên khách mới..." required>
                    </div>
                    <div>
                        <label>Số Điện Thoại</label>
                        <input type="text" name="phone" id="bPhone" placeholder="0912 345 678">
                    </div>
                </div>

                <div class="form-group" style="display:grid; grid-template-columns:2fr 1fr; gap:16px;">
                    <div>
                        <label>Phòng</label>
                        <select name="room_id" id="bRoomId" onchange="syncRoom(this)">
                            <?php foreach($rooms_list as $r): ?>
                            <option value="<?= $r['id'] ?>"
                                data-name="<?= htmlspecialchars($r['room_name']) ?>"
                                data-price="<?= $r['price'] ?>"
                                data-capacity="<?= $r['capacity'] ?>">
                                <?= htmlspecialchars($r['room_name']) ?> (<?= number_format($r['price'], 0, ',', '.') ?>đ/đêm, tối đa <?= $r['capacity'] ?> khách)
                            </option>
                            <?php endforeach; ?>
                            <?php if(empty($rooms_list)): ?>
                            <option value="0">-- Không có phòng trống --</option>
                            <?php endif; ?>
                            <option value="" id="noRoomOption" style="display:none;" disabled>-- Không có phòng phù hợp --</option>
                        </select>
                        <input type="hidden" name="room_name" id="bRoomName">
                    </div>
                    <div>
                        <label>Số Khách</label>
                        <input type="number" name="guests" id="bGuests" value="1" min="1" required onchange="filterRoomsByGuests(this.value)">
                        <p id="guestNote" style="font-size:12px; color:#64748b; margin-top:4px;"></p>
                    </div>
                </div>

                <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label>Ngày Nhận Phòng</label>
                        <input type="date" name="check_in" id="bCheckIn" required>
                    </div>
                    <div>
                        <label>Ngày Trả Phòng</label>
                        <input type="date" name="check_out" id="bCheckOut" required>
                    </div>
                </div>

                <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label>Tổng Tiền <small style="color:#94a3b8; font-weight:400;">(tự tính)</small></label>
                        <input type="number" name="total_price" id="bTotalPrice" readonly
                            style="background:#f8fafc; color:#334155; cursor:default;"
                            placeholder="Chọn phòng và ngày...">
                        <p id="priceNote" style="font-size: 12px; color: #64748b; margin-top: 6px;"></p>
                    </div>
                    <div>
                        <label>Trạng Thái</label>
                        <select name="status" id="bStatus">
                            <option value="pending">Chờ Xác Nhận</option>
                            <option value="confirmed">Đã Xác Nhận</option>
                            <option value="checked_in">Đã Nhận Phòng</option>
                            <option value="checked_out">Đã Trả Phòng</option>
                            <option value="cancelled">Đã Hủy</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ghi chú</label>
                    <input type="text" name="note" id="bNote" placeholder="Ghi chú thêm (không bắt buộc)">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeBookingModal()">Hủy</button>
                    <button type="submit" class="btn-dark">Lưu Đặt Phòng</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('liveSearch');
        const statusFilter = document.getElementById('statusFilter');
        const rows = document.querySelectorAll('#bookingTable tbody tr[data-name]');

        function filterTable() {
            const kw = searchInput.value.toLowerCase().trim();
            const sv = statusFilter.value;
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const room = row.dataset.room || '';
                const stat = row.dataset.status || '';
                const matchSearch = kw === '' || name.includes(kw) || room.includes(kw);
                const matchStatus = sv === '' || stat === sv;
                row.style.display = (matchSearch && matchStatus) ? '' : 'none';
            });
        }
        searchInput.addEventListener('input', filterTable);
        statusFilter.addEventListener('change', filterTable);
        const bModal = document.getElementById('bookingModal');
        function openBookingModal(mode, data = null) {
            bModal.classList.add('active');
            if (mode === 'add') {
                document.getElementById('bookingModalTitle').innerText = 'Đặt Phòng Mới';
                document.getElementById('bFormAction').value = 'add';
                document.getElementById('bookingForm').reset();
                document.getElementById('bGuestName').readOnly = false;
                document.getElementById('bPhone').readOnly = false;
                document.getElementById('bGuestName').style.backgroundColor = '';
                document.getElementById('bPhone').style.backgroundColor = '';
                syncRoomName(document.getElementById('bRoomId'));
            } else if (mode === 'edit' && data) {
                document.getElementById('bookingModalTitle').innerText = 'Chỉnh Sửa Đặt Phòng';
                document.getElementById('bFormAction').value = 'edit';
                document.getElementById('bId').value = data.id;
                document.getElementById('bGuestName').value = data.guest_name;
                document.getElementById('bPhone').value = data.phone || '';
                document.getElementById('bGuests').value = data.guests;
                document.getElementById('bCheckIn').value = data.check_in;
                document.getElementById('bCheckOut').value = data.check_out;
                document.getElementById('bTotalPrice').value = data.total_price;
                document.getElementById('bStatus').value = data.status;
                document.getElementById('bNote').value = data.note || '';
                document.getElementById('bRoomName').value = data.room_name;

                // Tự chọn phòng tương ứng
                const roomSel = document.getElementById('bRoomId');
                for (let opt of roomSel.options) {
                    if (opt.dataset.name === data.room_name) { roomSel.value = opt.value; break; }
                }

                // Tự chọn khách hàng đã liên kết
                const custSel = document.getElementById('bCustomerSelect');
                document.getElementById('bCustomerId').value = data.customer_id || '';
                custSel.value = data.customer_id || '';
                if (!data.customer_id) custSel.value = '';

                calcTotal();
            }
        }
        function closeBookingModal() { bModal.classList.remove('active'); }
        bModal.addEventListener('click', e => { if(e.target === bModal) closeBookingModal(); });

        function selectCustomer(sel) {
            const opt = sel.options[sel.selectedIndex];
            const custId = opt ? opt.value : '';
            const custName = opt ? (opt.dataset.name || '') : '';
            const custPhone = opt ? (opt.dataset.phone || '') : '';
            
            const nameInput = document.getElementById('bGuestName');
            const phoneInput = document.getElementById('bPhone');
            
            document.getElementById('bCustomerId').value = custId;
            
            if (custId && custPhone) {
                // Khách cũ có SĐT -> Khóa
                nameInput.value = custName;
                phoneInput.value = custPhone;
                nameInput.readOnly = true;
                phoneInput.readOnly = true;
                nameInput.style.backgroundColor = '#f1f5f9';
                phoneInput.style.backgroundColor = '#f1f5f9';
            } else {
                // Khách mới hoặc khách cũ không SĐT -> Mở
                if (custId) {
                    nameInput.value = custName;
                    phoneInput.value = custPhone;
                } else {
                    nameInput.value = '';
                    phoneInput.value = '';
                }
                nameInput.readOnly = false;
                phoneInput.readOnly = false;
                nameInput.style.backgroundColor = '';
                phoneInput.style.backgroundColor = '';
            }
        }

        // Lọc phòng theo số khách
        function filterRoomsByGuests(numGuests) {
            numGuests = parseInt(numGuests) || 1;
            const roomSel = document.getElementById('bRoomId');
            const allOpts = roomSel.querySelectorAll('option[data-capacity]');
            const note = document.getElementById('guestNote');
            let visibleCount = 0;

            allOpts.forEach(opt => {
                const cap = parseInt(opt.dataset.capacity) || 0;
                const show = numGuests <= 2 ? cap <= 2 : cap >= numGuests;
                opt.style.display = show ? '' : 'none';
                opt.disabled = !show;
                if (show) visibleCount++;
            });

            const noRoomOpt = document.getElementById('noRoomOption');

            if (visibleCount === 0) {
                if (noRoomOpt) {
                    noRoomOpt.style.display = '';
                    noRoomOpt.selected = true;
                }
                note.innerHTML = `<span style="color:#ef4444;"><i class="fa-solid fa-circle-exclamation"></i> Không có phòng phù hợp</span>`;
                syncRoom(roomSel);
            } else {
                if (noRoomOpt) noRoomOpt.style.display = 'none';
                const selected = roomSel.options[roomSel.selectedIndex];
                if (selected && (selected.disabled || selected.id === 'noRoomOption')) {
                    for (let o of roomSel.options) {
                        if (!o.disabled && o.value && o.id !== 'noRoomOption') { 
                            roomSel.value = o.value; 
                            break; 
                        }
                    }
                    syncRoom(roomSel);
                }
                note.innerHTML = '';
            }
        }

        function syncRoom(sel) {
            const opt = sel.options[sel.selectedIndex];
            document.getElementById('bRoomName').value = opt ? (opt.dataset.name || '') : '';
            calcTotal();
        }

        function calcTotal() {
            const roomSel = document.getElementById('bRoomId');
            const opt = roomSel.options[roomSel.selectedIndex];
            const pricePerNight = parseFloat(opt ? (opt.dataset.price || 0) : 0);

            const ci = document.getElementById('bCheckIn').value;
            const co = document.getElementById('bCheckOut').value;
            const note = document.getElementById('priceNote');
            const totalInput = document.getElementById('bTotalPrice');

            if (ci && co && pricePerNight > 0) {
                const d1 = new Date(ci);
                const d2 = new Date(co);
                const nights = Math.round((d2 - d1) / (1000*60*60*24));

                if (nights > 0) {
                    const total = nights * pricePerNight;
                    totalInput.value = total;
                    note.innerHTML = `<i class="fa-solid fa-calculator" style="color:#3b82f6;"></i> ${nights} đêm × ${pricePerNight.toLocaleString('vi-VN')}đ = <strong style="color:#0f172a;">${total.toLocaleString('vi-VN')}đ</strong>`;
                } else {
                    totalInput.value = '';
                    note.innerText = '⚠️ Ngày trả phải sau ngày nhận!';
                    note.style.color = '#ef4444';
                }
            } else {
                totalInput.value = '';
                note.innerText = '';
            }
        }

        document.getElementById('bCheckIn').addEventListener('change', calcTotal);
        document.getElementById('bCheckOut').addEventListener('change', calcTotal);

        syncRoom(document.getElementById('bRoomId'));

        // === LOGIC XOÁ NHIỀU ===
        function toggleSelectAll(master) {
            const checkboxes = document.querySelectorAll('.booking-checkbox');
            checkboxes.forEach(cb => {
                if (cb.parentElement.parentElement.style.display !== 'none') {
                    cb.checked = master.checked;
                }
            });
            updateBulkBtn();
        }

        function updateBulkBtn() {
            const checked = document.querySelectorAll('.booking-checkbox:checked');
            const btn = document.getElementById('bulkDeleteBtn');
            const countSpan = document.getElementById('selectedCount');
            
            if (checked.length > 0) {
                btn.style.display = 'inline-flex';
                countSpan.innerText = checked.length;
            } else {
                btn.style.display = 'none';
            }
        }

        function bulkDelete() {
            const checked = document.querySelectorAll('.booking-checkbox:checked');
            if (checked.length === 0) return;
            
            if (confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn ${checked.length} đơn đặt phòng đã chọn?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'bookings.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_multiple';
                form.appendChild(actionInput);
                
                checked.forEach(cb => {
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'ids[]';
                    idInput.value = cb.value;
                    form.appendChild(idInput);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>


