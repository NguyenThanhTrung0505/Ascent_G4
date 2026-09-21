<?php
require_once ROOT_PATH . '/app/models/student/Class.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'] ?? 'Học sinh';
$classId = (int)$_GET['class_id'];



$classData = [];
$rawBadge = getBage($studentId, $classId);
$rawExams = getAllExam($studentId, $classId);
$rawTopics = getAllTopic($studentId, $classId);
if ($rawBadge) {
    $classData['info'] = [
        'classId'     => (int)$rawBadge['class_id'],
        'className'   => $rawBadge['class_name'],
        'teacherName' => $rawBadge['teacher_name'] ?? 'Chưa phân công',
        'classIcon'   => $rawBadge['class_icon'] ?? '📚'
    ];

    $classData['stats'] = [
        'totalChapters'  => (int)$rawBadge['total_chapters'],
        'totalLessons'   => (int)$rawBadge['total_lessons'],
        'learnedLessons' => (int)$rawBadge['completed_lessons'],
        'avgScore'       => $rawBadge['avg_score'] !== null ? number_format((float)$rawBadge['avg_score'], 1) : '0.0'
    ];

    $classData['exams'] = array_map(function ($exam) use ($classId,  $studentId) {
        $examId = (int)$exam['exam_id'];
        $attemptId = startExamAttempt($examId, $studentId);
        return [
            'id'       => (int)$exam['exam_id'],
            'title'    => $exam['exam_title'] ?? 'Bài kiểm tra',
            'score'    => $exam['highest_score'] !== null ? (float)$exam['highest_score'] : '-',
            'maxScore' => (int)$exam['max_score'],
            'status'   => $exam['completion_status'],
            'date'     => !empty($exam['exam_date']) ? $exam['exam_date'] : '--/--/----',
            'link' => "index.php?page=student&action=take-exam"
                . "&class_id={$classId}"
                . "&exam_id={$examId}"
                . "&attempt_id={$attemptId}"
        ];
    }, $rawExams);

    $classData['chapters'] = array_map(function ($topic) {
        return [
            'id'      => (int)$topic['chapter_id'],
            'title'   => $topic['chapter_name'],
            'desc'    => $topic['chapter_description'] ?? 'Không có mô tả',
            'total'   => (int)$topic['total_chapter_lessons'],
            'learned' => (int)$topic['completed_chapter_lessons']
        ];
    }, $rawTopics);
};


$classDataJson = json_encode($classData, JSON_UNESCAPED_UNICODE);
require_once ROOT_PATH . '/app/views/pages/student/class.php';
