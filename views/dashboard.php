<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Horizon Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include __DIR__ . '/layouts/sidebar.php'; ?>

    <main class="main-content">
        <header class="page-header">
            <h1>Tổng Quan</h1>
            <p>Chào mừng trở lại <strong><?= htmlspecialchars($fullname) ?></strong>! Đây là tình hình hoạt động của khách sạn.</p>
        </header>

        <!-- KPI CARDS -->
        <section class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon blue">
                    <i class="fa-solid fa-bed"></i>
                </div>
                <div class="kpi-details">
                    <p class="kpi-title">Tổng Số Phòng</p>
                    <h3 class="kpi-value"><?= htmlspecialchars($total_rooms) ?></h3>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon indigo">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div class="kpi-details">
                    <p class="kpi-title">Phòng Đang Thuê</p>
                    <h3 class="kpi-value"><?= htmlspecialchars($rented_rooms) ?></h3>
                    <p class="kpi-trend positive">
                        <i class="fa-solid fa-arrow-trend-up"></i> <?= $occupancy_rate ?>% công suất
                    </p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon cyan">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <div class="kpi-details">
                    <p class="kpi-title">Tổng Khách</p>
                    <h3 class="kpi-value"><?= htmlspecialchars($total_customers) ?></h3>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon red">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="kpi-details">
                    <p class="kpi-title">Doanh Thu</p>
                    <h3 class="kpi-value"><?= htmlspecialchars($revenue_formatted) ?></h3>
                    <p class="kpi-trend positive">
                        <i class="fa-solid fa-arrow-trend-up"></i> Tháng <?= $current_month ?>/<?= $current_year ?>
                    </p>
                </div>
            </div>
        </section>

        <section class="charts-grid">
            <div class="chart-card">
                <h3>Tổng Quan Doanh Thu</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>Tỷ Lệ Lấp Đầy</h3>
                <div class="chart-container">
                    <canvas id="occupancyChart"></canvas>
                </div>
            </div>
        </section>
    </main>

    <script>
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');

        const monthlyRevenue = <?= json_encode($monthly_revenue) ?>;
        const monthlyOccupancy = <?= json_encode($monthly_occupancy) ?>;
        const monthLabels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];

        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{ label: 'Doanh Thu (VNĐ)', data: monthlyRevenue, backgroundColor: '#3b82f6', borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(c) { return 'Doanh Thu: ' + (c.raw||0).toLocaleString('vi-VN') + ' đ'; } } } },
                scales: { y: { beginAtZero: true, grid: { borderDash: [4,4] }, ticks: { callback: v => v>=1000000 ? (v/1000000)+' Tr' : v } }, x: { grid: { display: false } } }
            }
        });

        new Chart(occupancyCtx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{ label: 'Tỷ lệ %', data: monthlyOccupancy, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.4, fill: true, pointBackgroundColor: '#fff', pointBorderColor: '#3b82f6', pointBorderWidth: 2, pointRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => 'Tỷ lệ lấp đầy: ' + c.raw + '%' } } },
                scales: { y: { beginAtZero: true, max: 100, grid: { borderDash: [4,4] }, ticks: { callback: v => v + '%' } }, x: { grid: { display: false } } }
            }
        });
    </script>
</body>
</html>
