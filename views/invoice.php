<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn #<?= $id ?> - Horizon Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --bg: #f8fafc;
            --text: #334155;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg); color: var(--text); padding: 40px 20px; display: flex; justify-content: center; }
        .invoice-box { max-width: 800px; width: 100%; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 2px solid var(--border); padding-bottom: 20px; }
        .header-left h1 { font-family: 'Playfair Display', serif; color: var(--primary); font-size: 32px; margin-bottom: 5px; }
        .header-left p { color: #64748b; font-size: 14px; }
        .header-right { text-align: right; }
        .header-right h2 { color: var(--accent); font-size: 24px; margin-bottom: 5px; }
        .invoice-details { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .detail-group h3 { font-size: 14px; color: #94a3b8; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 0.5px; }
        .detail-group p { font-size: 15px; margin-bottom: 5px; color: var(--primary); font-weight: 500; }
        .detail-group p span { color: #64748b; font-weight: 400; width: 120px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: #f8fafc; color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: 600; }
        td { font-size: 14px; color: var(--primary); }
        td.amount { text-align: right; font-weight: 600; }
        th.amount { text-align: right; }
        .totals { border-top: 2px solid var(--primary); padding-top: 20px; text-align: right; }
        .totals p { font-size: 15px; margin-bottom: 10px; color: #64748b; }
        .totals p span { display: inline-block; width: 150px; font-weight: 600; color: var(--primary); }
        .grand-total { font-size: 24px !important; color: var(--accent) !important; font-weight: 700 !important; margin-top: 15px; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 120px; font-weight: 700; color: rgba(16, 185, 129, 0.05); font-family: 'Playfair Display', serif; pointer-events: none; }
        .watermark.unpaid { color: rgba(239, 68, 68, 0.05); }
        .actions { margin-top: 40px; display: flex; justify-content: flex-end; gap: 15px; }
        .btn { padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; display: flex; align-items: center; gap: 8px; transition: 0.3s; text-decoration: none; }
        .btn-print { background: var(--primary); color: white; }
        .btn-print:hover { background: #1e293b; }
        .btn-pay { background: #10b981; color: white; }
        .btn-pay:hover { background: #059669; }
        @media print {
            body { background: white; padding: 0; }
            .invoice-box { box-shadow: none; max-width: 100%; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-box" style="position: relative;">
    <?php if ($is_fully_paid): ?>
        <div class="watermark">ĐÃ THANH TOÁN</div>
    <?php else: ?>
        <div class="watermark unpaid">CÒN NỢ: <?= number_format($balance, 0, ',', '.') ?> đ</div>
    <?php endif; ?>

    <div class="header">
        <div class="header-left">
            <h1>Horizon Hotel</h1>
            <p>123 Đường Điện Biên Phủ, Quận 1, TP. HCM</p>
            <p>Hotline: 1900 1234 56 | Email: contact@horizonhotel.com</p>
        </div>
        <div class="header-right">
            <h2><?= $title_text ?></h2>
            <p style="color: #64748b; font-weight: 500;">Mã: #<?= $is_final ? 'INV' : 'PRO' ?>-<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?></p>
            <p style="color: #64748b; font-size: 13px; margin-top: 5px;">Ngày Tạo: <?= date('d/m/Y') ?></p>
        </div>
    </div>

    <div class="invoice-details">
        <div class="detail-group">
            <h3>Thông Tin Khách Hàng</h3>
            <p><span>Tên Khách Hàng:</span> <?= htmlspecialchars($booking['guest_name']) ?></p>
            <p><span>Số Điện Thoại:</span> <?= htmlspecialchars($booking['phone'] ?? '---') ?></p>
            <p><span>Địa Chỉ:</span> <?= htmlspecialchars($booking['address'] ?? '---') ?></p>
        </div>
        <div class="detail-group">
            <h3>Thông Tin Lưu Trú</h3>
            <p><span>Phòng:</span> <?= htmlspecialchars($booking['room_name']) ?> (<?= $booking['guests'] ?> Khách)</p>
            <p><span>Nhận Phòng:</span> <?= date('d/m/Y', strtotime($booking['check_in'])) ?></p>
            <p><span>Trả Phòng:</span> <?= date('d/m/Y', strtotime($booking['check_out'])) ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Diễn Giải Dịch Vụ / Phòng</th>
                <th>Danh Mục</th>
                <th style="text-align: center;">SL</th>
                <th class="amount">Thành Tiền</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Tiền Thuê Phòng</strong><br><small style="color:#64748b;">(Từ <?= date('d/m', strtotime($booking['check_in'])) ?> đến <?= date('d/m', strtotime($booking['check_out'])) ?>)</small></td>
                <td>Lưu trú</td>
                <td style="text-align: center;">1</td>
                <td class="amount"><?= number_format($booking['total_price'], 0, ',', '.') ?> đ</td>
            </tr>
            <?php foreach($services as $s): 
                $status_label = "";
                $status_color = "#64748b";
                if ($s['status'] === 'pending') { $status_label = "(Chờ xác nhận)"; }
                elseif ($s['status'] === 'processing') { $status_label = "(Đã xác nhận)"; $status_color = "#3b82f6"; }
                elseif ($s['status'] === 'completed') { $status_label = "(Đã hoàn thành)"; $status_color = "#10b981"; }
            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($s['service_name']) ?></strong>
                    <br><small style="color:<?= $status_color ?>; font-weight:600;"><?= $status_label ?></small>
                    <br><small style="color:#94a3b8;"><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></small>
                </td>
                <td>Dịch vụ</td>
                <td style="text-align: center;"><?= $s['quantity'] ?></td>
                <td class="amount"><?= number_format($s['total_price'], 0, ',', '.') ?> đ</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
        $paid_amount = $is_room_paid ? $grand_total_display : 0;
    ?>
    <div class="totals">
        <p><span>Tiền Phòng:</span> <?= number_format($booking['total_price'], 0, ',', '.') ?> đ</p>
        <p><span>Dịch Vụ:</span> <?= number_format($total_services, 0, ',', '.') ?> đ</p>
        
        <div style="margin: 15px 0; border-top: 1px dashed #e2e8f0; padding-top: 15px;">
            <p style="font-weight: 600; color: var(--primary);"><span>Tổng Bill:</span> <?= number_format($grand_total_display, 0, ',', '.') ?> đ</p>
            <p style="color: #10b981;"><span>Đã Thanh Toán:</span> - <?= number_format($paid_amount, 0, ',', '.') ?> đ</p>
            <p class="grand-total" style="margin-top: 10px; border-top: 2px solid var(--accent); padding-top: 10px;">
                <span>CÒN LẠI PHẢI TRẢ:</span> <?= number_format($balance, 0, ',', '.') ?> đ
            </p>
        </div>
        
        <p style="margin-top: 15px; font-size: 14px;"><strong>Trạng Thái:</strong> <span style="color: <?= $is_fully_paid ? '#10b981' : '#ef4444' ?>; font-weight:700;width:auto;"><?= $is_fully_paid ? 'ĐÃ THANH TOÁN TOÀN BỘ' : ($is_final ? 'CHƯA HOÀN TẤT THANH TOÁN' : 'ĐANG TRONG QUÁ TRÌNH LƯU TRÚ') ?></span></p>
    </div>

    <div class="actions">
        <?php if (!$is_fully_paid): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <form method="POST" action="index.php?route=invoice&id=<?= $id ?>" onsubmit="return confirm('Cập nhật trạng thái Đã Thanh Toán cho toàn bộ hóa đơn này?')">
                    <input type="hidden" name="action" value="pay">
                    <button type="submit" class="btn btn-pay"><i class="fa-solid fa-check"></i> Xác Nhận Thu Tiền Tại Quầy</button>
                </form>
            <?php else: ?>
                <button onclick="document.getElementById('qrModal').style.display='flex'" class="btn btn-pay" style="background:#2563eb;"><i class="fa-solid fa-qrcode"></i> Thanh Toán Online Balance</button>
            <?php endif; ?>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-print"></i> In Hóa Đơn</button>
    </div>
</div>

<!-- Customer QR Modal -->
<div id="qrModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; z-index:1000;">
    <div style="background:white; padding:40px; border-radius:12px; text-align:center; max-width:400px; width:90%;">
        <h3 style="color:var(--primary); font-family:'Playfair Display', serif; font-size:24px; margin-bottom:15px;">Thanh Toán QR Online</h3>
        <p style="color:#64748b; font-size:14px; margin-bottom:20px;">Vui lòng dùng ứng dụng ngân hàng hoặc MoMo/VNPay để quét mã. Tự động xác nhận sau khi chuyển khoản.</p>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=HORIZON_PAY_<?= $id ?>_<?= $balance ?>" alt="QR" style="border-radius:12px; border:1px solid #e2e8f0; margin-bottom:25px;">
        <h2 style="color:var(--accent); margin-bottom:25px;"><?= number_format($balance, 0, ',', '.') ?> đ</h2>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="document.getElementById('qrModal').style.display='none'" class="btn" style="background:#f1f5f9; color:#64748b;">Đóng</button>
            <form method="POST" action="index.php?route=invoice&id=<?= $id ?>">
                <input type="hidden" name="action" value="pay">
                <button type="submit" class="btn btn-pay" style="background:#2563eb;">Giả Mẫu: Đã Thanh Toán Xong</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>


