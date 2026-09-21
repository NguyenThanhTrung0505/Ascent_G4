<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/teacher/Chapter-list.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header("Location: index.php?page=auth&action=login");
    exit;
}

$teacherId = $_SESSION['user_id'];
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $data = [
            'class_id'    => $classId,
            'created_by'  => $teacherId,
            'name'        => trim($_POST['ten_chuong'] ?? ''),
            'title'       => trim($_POST['tieu_de_chuong'] ?? ''),
            'order_index' => isset($_POST['thu_tu']) ? (int)$_POST['thu_tu'] : 1
        ];

        if ($action === 'add') {
            $result = addChapter($data);
            $msg = $result ? "Thêm chương thành công!" : "Lỗi khi thêm chương.";
        } else {
            $chapterId = (int)$_POST['chapter_id'];
            $result = updateChapter($chapterId, $teacherId, $data);
            $msg = $result ? "Cập nhật thành công!" : "Lỗi khi cập nhật.";
        }

        echo json_encode(['success' => (bool)$result, 'message' => $msg]);
        exit;
    }

    if ($action === 'delete') {
        $chapterId = (int)$_POST['chapter_id'];
        $result = deleteChapter($chapterId, $teacherId);
        echo json_encode(['success' => (bool)$result, 'message' => $result ? "Đã xóa chương!" : "Không thể xóa chương này (Có thể đang chứa bài học)."]);
        exit;
    }
}

$limit = 10;
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $limit;

$totalChapters = getTotalChapters($classId);
$totalPages = ceil($totalChapters / $limit);

$chapters = getChapterList($classId, $limit, $offset);
$tenLop = "";

require_once ROOT_PATH . '/app/views/pages/teacher/chapter-list-teacher.php';
