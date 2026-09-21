<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/teacher/Update-question.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header("Location: index.php?page=auth&action=login");
    exit;
}

$teacherId = $_SESSION['user_id'];
$teacherName = $_SESSION['name'] ?? 'Giáo viên';
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$examId = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['exam']) || !isset($data['questions'])) {
        echo json_encode(['status' => 'error', 'message' => 'Dữ liệu gửi lên không hợp lệ.']);
        exit;
    }

    $examData = $data['exam'];
    $questionsList = $data['questions'];

    $result = updateExamTransaction($examId, $examData, $questionsList);

    echo json_encode($result);
    exit;
}
$examData = getExamFullDetails($examId);
$examJsonData = json_encode($examData);
$class_names = [1 => 'Toán 6C', 2 => 'Toán 6A'];
$class_name = $class_names[$examData['class_id']] ?? 'Lớp học';
$chapters = ['Chương 1: Phân số', 'Chương 2: Hình học'];

require_once ROOT_PATH . '/app/views/pages/teacher/update-question-teacher.php';
