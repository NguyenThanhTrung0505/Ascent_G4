<?php
require_once ROOT_PATH . '/app/models/admin/Dashboard.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$adminId = $_SESSION['user_id'];
$adminName = $_SESSION['name'] ?? 'Admin';
$badgeData = getBage();
$stats = [
    'teachers' => (int)($badgeData['total_teachers'] ?? 0),
    'students' => (int)($badgeData['total_students'] ?? 0),
    'classes'  => (int)($badgeData['total_classes'] ?? 0),
    'exams'    => (int)($badgeData['total_exams'] ?? 0)
];
$statsJson = json_encode($stats);
$recentActivities = getActive();
$formattedActivities = [];
if (!empty($recentActivities)) {
    foreach ($recentActivities as $act) {
        $time = date('d/m/Y H:i', strtotime($act['activity_time']));
        $formattedActivities[] = [
            'name' => $act['username'] ?? 'Người dùng ẩn',
            'desc' => $act['description'],
            'time' => $time
        ];
    }
}
$activitiesJson = json_encode($formattedActivities, JSON_UNESCAPED_UNICODE);
$activeWeekData = getActiveWeek();
$formattedWeekly = [];
$totalActivitiesWeek = 0;
if (!empty($activeWeekData)) {
    foreach ($activeWeekData as $day) {
        $formattedWeekly[] = [
            'label' => $day['day_label'],
            'value' => (int)$day['total_activities']
        ];
        $totalActivitiesWeek += (int)$day['total_activities'];
    }
} else {
    $formattedWeekly = [
        ['label' => 'T2', 'value' => 0],
        ['label' => 'T3', 'value' => 0],
        ['label' => 'T4', 'value' => 0],
        ['label' => 'T5', 'value' => 0],
        ['label' => 'T6', 'value' => 0],
        ['label' => 'T7', 'value' => 0],
        ['label' => 'CN', 'value' => 0]
    ];
}
$weeklyDataJson = json_encode($formattedWeekly, JSON_UNESCAPED_UNICODE);
require_once ROOT_PATH . '/app/views/pages/admin/dashboard.php';
