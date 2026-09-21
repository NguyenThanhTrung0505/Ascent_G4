<?php
require_once ROOT_PATH . '/app/models/student/Dashboard.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['name'] ?? 'Học sinh';
$classCount   = getJoinClassNumber($studentId);
$subject_list = getJoinSubject($studentId);
$currentExams = getExamCurrent($studentId);
$upcomingCount = getExamUpcoming($studentId);
$avgScore     = getGradeAvg($studentId);
$tasksList    = getUpcomingTasksList($studentId);
$dashboardData = [
    'stats' => [
        [
            'theme' => 'blue',
            'icon'  => '📚',
            'value' => $classCount,
            'title' => 'Lớp học tham gia',
            'desc'  => $subject_list ?: 'Chưa có môn học'
        ],
        [
            'theme' => 'orange',
            'icon'  => '⏳',
            'value' => $currentExams,
            'title' => 'Bài thi trong tuần',
            'desc'  => 'Cần hoàn thành sớm'
        ],
        [
            'theme' => 'purple',
            'icon'  => '📅',
            'value' => $upcomingCount,
            'title' => 'Bài thi sắp tới',
            'desc'  => 'Chuẩn bị ôn tập'
        ],
        [
            'theme' => 'green',
            'icon'  => '⭐',
            'value' => $avgScore,
            'title' => 'Điểm trung bình',
            'desc'  => 'Điểm cao nhất các bài'
        ]
    ],
    'upcomingTasks' => $tasksList
];
$dashboardDataJson = json_encode($dashboardData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

require_once ROOT_PATH . '/app/views/pages/student/dashboard.php';
