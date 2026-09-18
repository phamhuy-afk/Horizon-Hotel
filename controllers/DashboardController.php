<?php
namespace Controllers;

use Models\Room;
use Models\Customer;
use Models\Booking;
use Models\ServiceOrder;
use DateTime;

class DashboardController {
    private $roomModel;
    private $customerModel;
    private $bookingModel;
    private $serviceOrderModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
            header("Location: index.php?route=login");
            exit;
        }
        $this->roomModel = new Room();
        $this->customerModel = new Customer();
        $this->bookingModel = new Booking();
        $this->serviceOrderModel = new ServiceOrder();
    }

    public function index() {
        $fullname = $_SESSION['fullname'] ?? 'Admin';
        $email = $_SESSION['email'] ?? 'admin@hotel.com';

        $total_rooms = $this->roomModel->getCount();
        $rented_rooms = $this->roomModel->getCount('rented');
        $occupancy_rate = $total_rooms > 0 ? round(($rented_rooms / $total_rooms) * 100) : 0;
        $total_customers = $this->customerModel->getCount();

        $current_month = date('m');
        $current_year = date('Y');

        // Doanh thu trong tháng
        $bookings_this_month = $this->bookingModel->getActiveBookingsForYear($current_year);
        $booking_rev = 0;
        foreach ($bookings_this_month as $b) {
            if (date('m', strtotime($b['check_in'])) == $current_month && date('Y', strtotime($b['check_in'])) == $current_year) {
                $booking_rev += (float) $b['total_price'];
            }
        }

        $service_rev = $this->serviceOrderModel->getMonthlyRevenue($current_month, $current_year);
        $revenue = $booking_rev + $service_rev;

        $revenue_formatted = $this->formatMoneyDashboard($revenue);

        // Biểu đồ
        $monthly_revenue = [];
        $monthly_occupancy = [];
        $total_rooms_count = $total_rooms ?: 1;

        for ($m = 1; $m <= 12; $m++) {
            $month_str = str_pad($m, 2, '0', STR_PAD_LEFT);
            $start_date = "$current_year-$month_str-01";
            $end_date = date("Y-m-t", strtotime($start_date));
            $days_in_month = (int) date("t", strtotime($start_date));

            $rev_m = $this->serviceOrderModel->getMonthlyRevenue($month_str, $current_year);
            $booked_nights_m = 0;

            foreach ($bookings_this_month as $b) {
                $ci = $b['check_in'];
                $co = $b['check_out'];

                if (date('m', strtotime($ci)) == $month_str && date('Y', strtotime($ci)) == $current_year) {
                    $rev_m += (float) $b['total_price'];
                }

                // Tính tỉ lệ phòng lấp đầy
                if ($ci <= $end_date && $co >= $start_date) {
                    $overlap_start = max($ci, $start_date);
                    $overlap_end = min($co, $end_date);

                    $d_start = new DateTime($overlap_start);
                    $d_end = new DateTime($overlap_end);
                    $diff_days = $d_end->diff($d_start)->days;
                    if ($diff_days > 0) {
                        $booked_nights_m += $diff_days;
                    }
                }
            }

            $monthly_revenue[] = $rev_m;
            $total_available_nights = $total_rooms_count * $days_in_month;
            $occ_rate = $total_available_nights > 0 ? round(($booked_nights_m / $total_available_nights) * 100, 1) : 0;
            if ($occ_rate > 100) $occ_rate = 100;
            $monthly_occupancy[] = $occ_rate;
        }

        include __DIR__ . '/../views/dashboard.php';
    }

    private function formatMoneyDashboard($number) {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . ' tỷ đ';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . ' triệu đ';
        } else {
            return number_format($number, 0, ',', '.') . ' đ';
        }
    }
}
