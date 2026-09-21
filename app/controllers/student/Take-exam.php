<?php
require_once ROOT_PATH . '/app/models/student/Take-exam.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'] ?? 'Học sinh';
$examId    = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
$attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
if ($examId <= 0 || $attemptId <= 0) {
    header("Location: index.php?page=student&action=many-class");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_answer') {
        $qId = (int)($_POST['question_id'] ?? 0);
        $optId = (int)($_POST['option_id'] ?? 0);
        if ($qId > 0 && $optId > 0) {
            saveStudentAnswer($attemptId, $qId, $optId);
        }
        exit();
    }

    if ($_POST['action'] === 'submit_exam') {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        $result = gradeExamAttempt($attemptId, $studentId, $examId);
        echo json_encode([
            'success' => true,
            'data'    => $result
        ]);
        exit();
    }
}
$examInfo = getTime($studentId, $attemptId);
if (!$examInfo) {
    die("Lượt thi không hợp lệ hoặc đã được nộp!");
}
$examTitle = $examInfo['exam_title'];
$durationInSeconds = (int)$examInfo['remaining_seconds'];
if ($durationInSeconds <= 0) {
    finishExamAttempt($attemptId, $studentId);
    die("Bài thi đã hết thời gian làm bài!");
}
$rawQuestions = getAllQuestionsWithOptions($examId, $attemptId);
$questionsGrouped = [];
$initialAnswers = [];
$useRealData = false;
if (!empty($rawQuestions)) {
    foreach ($rawQuestions as $row) {
        $qId = $row['question_id'];
        if (!isset($questionsGrouped[$qId])) {
            $questionsGrouped[$qId] = [
                'id'          => $qId,
                'text'        => $row['question_content'],
                'options'     => [],
                'option_ids'  => [],
                'selected_idx' => null
            ];
        }
        $currentIndex = count($questionsGrouped[$qId]['options']);
        $questionsGrouped[$qId]['options'][]    = $row['option_content'];
        $questionsGrouped[$qId]['option_ids'][] = $row['option_id'];
        if ($row['is_selected'] == 1) {
            $questionsGrouped[$qId]['selected_idx'] = $currentIndex;
        }
    }
    $formattedQuestions = array_values($questionsGrouped);
    $initialAnswers = array_column($formattedQuestions, 'selected_idx');
    $useRealData = true;
} else {
    $formattedQuestions = [];
    $initialAnswers = [];
    $useRealData = false;
}
$questionsJson = json_encode($formattedQuestions, JSON_UNESCAPED_UNICODE);
$initialAnswersJson = json_encode($initialAnswers);
require_once ROOT_PATH . '/app/views/pages/student/take-exam.php';
