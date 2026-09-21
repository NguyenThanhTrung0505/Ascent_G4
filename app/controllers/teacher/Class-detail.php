<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/teacher/Class-detail.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header("Location: index.php?page=auth&action=login");
    exit;
}

$teacherId = $_SESSION['user_id'];
$teacherName = $_SESSION['name'] ?? 'Giáo viên';
$id_lop = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api'])) {
    try {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        if ($_GET['api'] === 'create-chapter') {
            $id_lop_api = (int)($_POST['class_id'] ?? $_GET['id'] ?? 0);
            if ($id_lop_api <= 0) {
                echo json_encode(['success' => false, 'message' => 'LỖI: Mất kết nối với ID Lớp học (ID = 0). Vui lòng tải lại trang!']);
                exit;
            }
            $tenChuong = trim($_POST['ten_chuong'] ?? '');
            $moTa      = trim($_POST['mo_ta'] ?? '');
            $icon      = trim($_POST['icon_chuong'] ?? '📊');
            if (empty($tenChuong)) {
                echo json_encode(['success' => false, 'message' => 'Tên chương không được để trống.']);
                exit;
            }

            createNewChapter($id_lop_api, $tenChuong, $moTa, $teacherId);
            echo json_encode(['success' => true]);
            exit;
        }
        if ($_GET['api'] === 'add-student') {
            $id_lop_api = (int)($_POST['class_id'] ?? $_GET['id'] ?? 0);
            $email = trim($_POST['email'] ?? '');
            if ($id_lop_api <= 0) {
                echo json_encode(['success' => false, 'message' => 'Lỗi: Không xác định được ID lớp học.']);
                exit;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Email không đúng định dạng.']);
                exit;
            }
            $result = addStudentToClass($id_lop_api, $email);
            echo json_encode($result);
            exit;
        }
    } catch (\Throwable $th) {
        if (ob_get_length()) ob_clean();
        echo json_encode(['success' => false, 'message' => 'Lỗi Backend: ' . $th->getMessage()]);
        exit;
    }
}
$overviewData = getClassOverview($id_lop);
if (!$overviewData) {
    $thong_tin_lop = [
        'id' => 0,
        'ten_lop' => 'Không tìm thấy lớp học',
        'so_hoc_sinh' => 0,
        'icon_mon_hoc' => '❌',
        'tong_so_chuong' => 0,
        'tong_bai_hoc' => 0,
        'tong_bai_kiem_tra' => 0,
        'tien_do_lop' => 'Trống',
        'danh_sach_bai_kiem_tra' => [],
        'danh_sach_chuong' => [],
    ];
} else {
    $examsData = getRecentExams($id_lop);
    $chaptersData = getRecentChapters($id_lop);

    $dsKiemTra = [];
    foreach ($examsData as $ex) {
        $dsKiemTra[] = [
            'id'    => $ex['exam_id'],
            'tieu_de' => $ex['exam_name'] . ($ex['exam_title'] ? " - " . $ex['exam_title'] : ""),
            'phan_tram' => $ex['completion_percentage'] ?? 0,
            'so_da_hoan_thanh' => $ex['completed_students'] ?? 0,
            'tong_so_hoc_sinh' => $ex['total_students'] ?? 0,
            'ngay_ket_thuc' => $ex['end_date_formatted'],
            'link_chi_tiet' => "index.php?page=exam-detail&id=" . $ex['exam_id']
        ];
    }

    $dsChuong = [];
    foreach ($chaptersData as $ch) {
        $dsChuong[] = [
            'id' => $ch['chapter_id'],
            'ten_chuong' => $ch['chapter_name'],
            'mo_ta' => $ch['chapter_title'],
            'phan_tram' => $ch['progress_percentage'] ?? 0,
            'so_bai_hoc' => $ch['total_lessons'] ?? 0,
            'so_bai_hoan_thanh' => $ch['completed_lessons_avg'] ?? 0,
            'link_chi_tiet' => "index.php?page=teacher&action=chapter-detail&class_id=" . $id_lop . "&chapter_id=" . $ch['chapter_id']
        ];
    }

    $thong_tin_lop = [
        'id' => $overviewData['class_id'],
        'ten_lop' => $overviewData['class_name'],
        'so_hoc_sinh' => $overviewData['total_students'],
        'icon_mon_hoc' => $overviewData['icon'] ?: '📘',
        'tong_so_chuong' => $overviewData['total_chapters'],
        'tong_bai_hoc' => $overviewData['total_lessons'],
        'tong_bai_thi_kiem_tra' => $overviewData['total_exams'],
        'tien_do_lop' => $overviewData['current_progress_chapter'],
        'danh_sach_bai_kiem_tra' => $dsKiemTra,
        'danh_sach_chuong' => $dsChuong,
    ];
}
require_once ROOT_PATH . '/app/views/pages/teacher/class-detail-teacher.php';
