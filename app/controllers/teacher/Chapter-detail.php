<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/teacher/Chapter-detail.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header("Location: index.php?page=auth&action=login");
    exit;
}

$teacherId = $_SESSION['user_id'];
$teacherName = $_SESSION['name'] ?? 'Giáo viên';
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$chapterId = isset($_GET['chapter_id']) ? (int)$_GET['chapter_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $file_name = null;
        $file_type = null;
        $file_data = null;
        if (isset($_FILES['tai_lieu']) && $_FILES['tai_lieu']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['tai_lieu']['size'] > 20971520) {
                echo json_encode(['success' => false, 'message' => 'File quá lớn. Tối đa 20MB.']);
                exit;
            }
            $file_name = $_FILES['tai_lieu']['name'];
            $file_type = $_FILES['tai_lieu']['type'];
            $file_data = file_get_contents($_FILES['tai_lieu']['tmp_name']);
        }
        $data = [
            'chapter_id'  => $chapterId,
            'created_by'  => $teacherId,
            'name'        => $_POST['ten_bai_hoc'] ?? '',
            'title'       => $_POST['mo_ta_bai_hoc'] ?? '',
            'order_index' => isset($_POST['thu_tu']) ? (int)$_POST['thu_tu'] : 1,
            'content'     => $_POST['noi_dung'] ?? '',
            'file_name'   => $file_name,
            'file_type'   => $file_type,
            'file_data'   => $file_data
        ];

        if ($action === 'add') {
            $result = addLesson($data);
            $msg = $result ? "Thêm bài học thành công!" : "Lỗi khi thêm bài học.";
        } else {
            $lessonId = (int)$_POST['lesson_id'];
            $result = updateLesson($lessonId, $teacherId, $data);
            $msg = $result ? "Cập nhật thành công!" : "Lỗi khi cập nhật.";
        }

        echo json_encode(['success' => (bool)$result, 'message' => $msg]);
        exit;
    }

    if ($action === 'delete') {
        $lessonId = (int)$_POST['lesson_id'];
        $result = deleteLesson($lessonId, $teacherId);
        echo json_encode([
            'success' => (bool)$result,
            'message' => $result ? "Đã xóa bài học!" : "Không thể xóa bài học này."
        ]);
        exit;
    }
}

$chapterInfo = getChapterInfo($chapterId);
$tenChuong = $chapterInfo['ten_chuong'] ?? 'Không tìm thấy chương';
$tenLop = $chapterInfo['mo_ta'] ?? "";

$limit = 10;
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $limit;

$totalLessons = getTotalLessons($chapterId);
$totalPages = ceil($totalLessons / $limit);

$lessons = getLessonList($chapterId, $limit, $offset);
require_once ROOT_PATH . '/app/views/pages/teacher/chapter-detail-teacher.php';
