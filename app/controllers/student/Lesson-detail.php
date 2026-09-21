<?php
require_once ROOT_PATH . '/app/models/student/Lesson-detail.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'] ?? 'Học sinh';
$lessonId  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $jsonInput = file_get_contents("php://input");
    $data = json_decode($jsonInput, true);

    if (isset($data['action']) && $data['action'] === 'mark_done') {
        $isSaved = handleButtonDone($studentId, $lessonId);

        if (ob_get_length()) ob_clean(); // Dọn rác HTML thừa

        if ($isSaved) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Lỗi DB.']);
        }
        exit;
    }
}
$lessonData = getLessonInfo($studentId, $lessonId);
$lesson = [
    "title"             => $lessonData['lesson_title'] ?: $lessonData['lesson_name'],
    "chapter_title"     => $lessonData['chapter_name'],
    "chapter_id"        => $lessonData['chapter_id'],
    "subject"           => $lessonData['subject_name'],
    "section1_title"    => "Nội dung bài học",
    "section1_content"  => $lessonData['lesson_content'],
    "highlight_label"   => "Tài liệu đính kèm",
    "highlight_content" => $lessonData['file_name'] ?? 'Không có tài liệu',
    "section2_title"    => "",
    "section2_content"  => "",
    "is_completed"      => (bool)$lessonData['is_completed']
];
require_once ROOT_PATH . '/app/views/pages/student/lesson-detail.php';
