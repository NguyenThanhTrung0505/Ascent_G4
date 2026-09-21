<?php
require_once ROOT_PATH . '/app/models/student/Many-class.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'] ?? 'Học sinh';

$classesFormatted = [];
$themeColors = ['blue', 'green', 'orange'];
$rawClasses = getAllClassByStudentId($studentId);
foreach ($rawClasses as $index => $row) {
    $classesFormatted[] = [
        'id'       => $row['class_id'],
        'name'     => $row['class_name'],
        'teacher'  => $row['teacher_name'] ?? 'Chưa phân công',
        'category' => $row['subject_name'] ?? 'Chung',
        'icon'     => !empty($row['class_icon']) ? $row['class_icon'] : '📚',
        'theme'    => $themeColors[$index % count($themeColors)]
    ];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api']) && $_GET['api'] === 'join-class') {
    header('Content-Type: application/json; charset=utf-8');
    $classCode = trim($_POST['class_code'] ?? '');
    if (empty($classCode)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã lớp.']);
        exit;
    }
    $result = joinClassByCode($studentId, $classCode);
    echo json_encode($result);
    exit;
}
$classesDataJson = json_encode($classesFormatted, JSON_UNESCAPED_UNICODE);
require_once ROOT_PATH . '/app/views/pages/student/many-class.php';
