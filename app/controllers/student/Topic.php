<?php
require_once ROOT_PATH . '/app/models/student/Topic.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'] ?? 'Học sinh';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $jsonInput = file_get_contents("php://input");
    $data = json_decode($jsonInput, true);
    if (!isset($data['lesson_id']) || !isset($data['done'])) {
        echo json_encode(['success' => false, 'error' => 'Thiếu tham số bắt buộc.']);
        exit;
    }
    $lessonId = (int)$data['lesson_id'];
    $isCompleted = (bool)$data['done'];
    $isSaved = updateLessonProgress($studentId, $lessonId, $isCompleted);
    ob_clean();
    if ($isSaved) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi cơ sở dữ liệu khi cập nhật tiến độ.']);
    }
    exit;
}
$topicId    = isset($_GET['topic_id']) ? (int)$_GET['topic_id'] : 0;

$chapterInfo = getLessonNumber($topicId, $studentId);
$chapter =
    [
        "title"     => $chapterInfo['chapter_name'],
        "className" => $chapterInfo['class_name'],
        "subject"   => "Môn học"
    ];
$totalCount = $chapterInfo['total_lessons'];
$doneCount  = $chapterInfo['completed_lessons'];
$listLessons = getListLesson($topicId, $studentId);
$formattedLessons = [];
foreach ($listLessons as $lesson) {
    $formattedLessons[] = [
        'id'    => (int)$lesson['lesson_id'],
        'title' => $lesson['lesson_name'],
        'desc'  => $lesson['lesson_description'] ?? 'Không có mô tả',
        'done'  => (bool)$lesson['is_completed']
    ];
}
$lessonsJson = json_encode($formattedLessons, JSON_UNESCAPED_UNICODE);
require_once ROOT_PATH . '/app/views/pages/student/topic.php';
