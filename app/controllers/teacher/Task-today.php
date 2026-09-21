<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/teacher/Task-today.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header("Location: index.php?page=auth&action=login");
    exit;
}

$teacherId = $_SESSION['user_id'];
$teacherName = $_SESSION['name'] ?? 'Giáo viên';

$limit = 5;
$examPage = isset($_GET['exam_page']) ? max(1, (int)$_GET['exam_page']) : 1;
$chapterPage = isset($_GET['chapter_page']) ? max(1, (int)$_GET['chapter_page']) : 1;

$examOffset = ($examPage - 1) * $limit;
$chapterOffset = ($chapterPage - 1) * $limit;

$totalExams = getAllExam($teacherId);
$totalExamPages = ceil($totalExams / $limit);

$totalChapters = getAllTopic($teacherId);
$totalChapterPages = ceil($totalChapters / $limit);

$rawExams = getExamList($teacherId, $limit, $examOffset);
$rawChapters = getTopicList($teacherId, $limit, $chapterOffset);
$rawTasks = getTodayList($teacherId);

$today_exams = [];
foreach ($rawExams as $exam) {
    $today_exams[] = [
        'title' => htmlspecialchars($exam['exam_title']),
        'meta'  => $exam['total_questions'] . ' câu hỏi | Thời gian: ' . $exam['duration'] . ' phút'
    ];
}

$today_chapters = [];
foreach ($rawChapters as $chapter) {
    $today_chapters[] = [
        'title' => htmlspecialchars($chapter['chapter_name']),
        'meta'  => 'Số bài giảng: ' . $chapter['total_lessons']
    ];
}

$today_tasks = [];
foreach ($rawTasks as $task) {
    $dueDate = strtotime($task['due_date']);
    $timeDiff = $dueDate - time();
    $status = ($timeDiff < 86400) ? 'danger' : (($timeDiff < 259200) ? 'warning' : 'success');
    $today_tasks[] = [
        'icon'     => '<i class="fa-solid fa-file-signature"></i>',
        'title'    => htmlspecialchars($task['task_name']),
        'chapter'  => 'Đề thi',
        'deadline' => date('d/m/Y H:i', $dueDate),
        'status'   => $status
    ];
}
require_once ROOT_PATH . '/app/views/pages/teacher/task-today-teacher.php';
