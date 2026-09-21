<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/teacher/Dashboard.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header("Location: index.php?page=auth&action=login");
    exit;
}

$teacherId = $_SESSION['user_id'];
$teacherName = $_SESSION['name'] ?? 'Giáo viên';

$stats = getTeacherDashboardStats($teacherId);
$so_chuong_hoc       = $stats['so_chuong'];
$so_de_thi           = $stats['so_de'];
$so_lop_hoc          = $stats['so_lop'];
$so_nhiem_vu_hom_nay = $stats['so_nhiem_vu'];

$rawExams = getOngoingExams($teacherId, 3);
$danh_sach_bai_tap = [];
foreach ($rawExams as $item) {
    $color = 'orange';
    $danh_sach_bai_tap[] = [
        'tieu_de'          => $item['tieu_de'],
        'ten_lop'          => $item['ten_lop'],
        'mau_the_loai'     => $color,
        'so_da_hoan_thanh' => (int) $item['so_da_hoan_thanh'],
        'tong_so_hoc_sinh' => (int) $item['tong_so_hoc_sinh'],
        'ngay_ket_thuc' => !empty($item['due_date'])
            ? date('d/m/Y', strtotime($item['due_date']))
            : 'Không giới hạn',

        'link_chi_tiet' => 'index.php?page=teacher&action=exam-detail&id=' . $item['id'],
    ];
}

require_once ROOT_PATH . '/app/views/pages/teacher/dashboard-teacher.php';
