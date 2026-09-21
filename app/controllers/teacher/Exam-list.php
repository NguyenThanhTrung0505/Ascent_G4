<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/teacher/Exam-list.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header("Location: index.php?page=auth&action=login");
    exit;
}

$teacherId = $_SESSION['user_id'];
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $examId = (int)$_POST['exam_id'];
        $result = deleteExamTransaction($examId, $teacherId);

        echo json_encode([
            'success' => $result,
            'message' => $result ? "Đã xóa đề thi thành công!" : "Không thể xóa đề thi này."
        ]);
        exit;
    }
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
}

$limit = 10;
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $limit;

$totalExams = getTotalExams($teacherId);
$totalPages = ceil($totalExams / $limit);

$exams = getExamList($teacherId, $limit, $offset);

require_once ROOT_PATH . '/app/views/pages/teacher/exam-list-teacher.php';
