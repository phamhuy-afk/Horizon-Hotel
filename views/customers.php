<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khách Hàng - Horizon Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <main class="main-content">
        <header class="page-header" style="display:flex; justify-content:space-between; align-items:flex-end;">
            <div>
                <h1>Khách Hàng</h1>
                <p>Quản lý thông tin khách hàng của khách sạn</p>
            </div>
            <button class="btn-dark" onclick="openCModal('add')">
                <i class="fa-solid fa-plus"></i> Thêm Khách Hàng
            </button>
        </header>

        <!-- SEARCH -->
        <div class="toolbar">
            <div class="search-box" style="flex:1;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="liveSearch" placeholder="Tìm kiếm khách hàng theo tên hoặc email..." autocomplete="off">
            </div>
            <div class="filter-box">
                <select id="typeFilter">
                    <option value="">Tất Cả</option>
                    <option value="normal">Thường</option>
                    <option value="vip">VIP</option>
                </select>
            </div>
        </div>

        <!-- TABLE -->
        <div class="booking-table-wrap">
            <table class="booking-table" id="customerTable">
                <thead>
                    <tr>
                        <th>Mã KH</th>
                        <th>Họ Tên</th>
                        <th>Liên Hệ</th>
                        <th>Địa Chỉ</th>
                        <th style="text-align:center;">Tổng Đặt Phòng</th>
                        <th style="text-align:center;">Trạng Thái</th>
                        <th style="text-align:center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($customers)): ?>
                    <tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:40px;">Chưa có khách hàng nào.</td></tr>
                <?php else: ?>
                <?php foreach($customers as $c): ?>
                <tr data-name="<?= strtolower(htmlspecialchars($c['fullname'])) ?>"
                    data-email="<?= strtolower(htmlspecialchars($c['email'])) ?>"
                    data-type="<?= $c['type'] ?>">
                    <td class="booking-id">#<?= $c['id'] ?></td>
                    <td class="guest-name"><?= htmlspecialchars($c['fullname']) ?></td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:4px;">
                            <?php if($c['email']): ?>
                            <span style="color:#3b82f6; font-size:13px;">
                                <i class="fa-regular fa-envelope" style="margin-right:5px;"></i><?= htmlspecialchars($c['email']) ?>
                            </span>
                            <?php endif; ?>
                            <?php if($c['phone']): ?>
                            <span style="color:#64748b; font-size:13px;">
                                <i class="fa-solid fa-phone" style="margin-right:5px;"></i><?= htmlspecialchars($c['phone']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if($c['address']): ?>
                        <span style="color:#475569; font-size:13px;">
                            <i class="fa-solid fa-location-dot" style="color:#3b82f6; margin-right:5px;"></i><?= htmlspecialchars($c['address']) ?>
                        </span>
                        <?php else: ?><span style="color:#cbd5e1;">—</span><?php endif; ?>
                    </td>
                    <td style="text-align:center; font-weight:600; color:#1e293b;"><?= $c['total_bookings'] ?></td>
                    <td style="text-align:center;">
                        <?php if($c['type'] === 'vip'): ?>
                            <span class="cust-badge cust-vip">VIP</span>
                        <?php else: ?>
                            <span class="cust-badge cust-normal">Thường</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <button class="btn-action edit" onclick="openCModal('edit', <?= htmlspecialchars(json_encode($c)) ?>)" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="index.php?route=customers&action=delete&id=<?= $c['id'] ?>" class="btn-action delete"
                               onclick="return confirm('Xóa khách hàng <?= htmlspecialchars($c['fullname']) ?>?')" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!--  THÊM / SỬA KHÁCH HÀNG -->
    <div class="modal-overlay" id="cModal">
        <div class="modal-box" style="max-width:520px;">
            <button class="modal-close" onclick="closeCModal()"><i class="fa-solid fa-xmark"></i></button>
            <h2 class="modal-title" id="cModalTitle">Thêm Khách Hàng</h2>

            <form action="index.php?route=customers" method="POST" id="cForm">
                <input type="hidden" name="action" id="cAction" value="add">
                <input type="hidden" name="id" id="cId">

                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="fullname" id="cFullname" placeholder="VD: Nguyễn Văn An" required>
                </div>

                <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" id="cEmail" placeholder="example@email.com">
                    </div>
                    <div>
                        <label>Số Điện Thoại</label>
                        <input type="text" name="phone" id="cPhone" placeholder="0912 345 678">
                    </div>
                </div>

                <div class="form-group">
                    <label>Địa Chỉ</label>
                    <input type="text" name="address" id="cAddress" placeholder="VD: 123 Nguyễn Huệ, Quận 1, TP.HCM">
                </div>

                <div class="form-group">
                    <label>Loại Khách Hàng</label>
                    <select name="type" id="cType">
                        <option value="normal">Thường</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeCModal()">Hủy</button>
                    <button type="submit" class="btn-dark">Lưu Khách Hàng</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('liveSearch');
        const typeFilter = document.getElementById('typeFilter');
        const rows = document.querySelectorAll('#customerTable tbody tr[data-name]');

        function filterTable() {
            const kw = searchInput.value.toLowerCase().trim();
            const tv = typeFilter.value;
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const type = row.dataset.type || '';
                const matchSearch = kw === '' || name.includes(kw) || email.includes(kw);
                const matchType = tv === '' || type === tv;
                row.style.display = (matchSearch && matchType) ? '' : 'none';
            });
        }
        searchInput.addEventListener('input', filterTable);
        typeFilter.addEventListener('change', filterTable);
        const cModal = document.getElementById('cModal');

        function openCModal(mode, data = null) {
            cModal.classList.add('active');
            if (mode === 'add') {
                document.getElementById('cModalTitle').innerText = 'Thêm Khách Hàng';
                document.getElementById('cAction').value = 'add';
                document.getElementById('cForm').reset();
            } else if (mode === 'edit' && data) {
                document.getElementById('cModalTitle').innerText = 'Chỉnh Sửa Khách Hàng';
                document.getElementById('cAction').value = 'edit';
                document.getElementById('cId').value = data.id;
                document.getElementById('cFullname').value = data.fullname;
                document.getElementById('cEmail').value = data.email || '';
                document.getElementById('cPhone').value = data.phone || '';
                document.getElementById('cAddress').value = data.address || '';
                document.getElementById('cType').value = data.type;
            }
        }
        function closeCModal() { cModal.classList.remove('active'); }
        cModal.addEventListener('click', e => { if(e.target === cModal) closeCModal(); });
    </script>
</body>
</html>


