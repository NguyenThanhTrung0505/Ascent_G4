<?php
require_once ROOT_PATH . '/app/models/admin/Exam.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$adminId = $_SESSION['user_id'];
$adminName = $_SESSION['name'] ?? 'Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $jsonInput = file_get_contents("php://input");
    $req = json_decode($jsonInput, true);
    $action = $req['action'] ?? '';
    if (ob_get_length()) ob_clean();
    try {
        if ($action === 'add') {
            $startTime = !empty($req['start_time']) ? $req['start_time'] : null;
            $endTime = !empty($req['end_time']) ? $req['end_time'] : null;
            $res = addExam(
                $req['class_id'],
                $adminId,
                $req['name'],
                $req['title'] ?? '',
                $req['duration'],
                $req['attempts'] ?? 1,
                $startTime,
                $endTime,
                $req['status'] ?? 'upcoming'
            );
            echo json_encode(['success' => $res]);
        } elseif ($action === 'edit') {
            $startTime = !empty($req['start_time']) ? $req['start_time'] : null;
            $endTime = !empty($req['end_time']) ? $req['end_time'] : null;
            $res = updateExam(
                $req['id'],
                $req['class_id'],
                $req['name'],
                $req['title'] ?? '',
                $req['duration'],
                $req['attempts'],
                $startTime,
                $endTime,
                $req['status']
            );
            echo json_encode(['success' => $res]);
        } elseif ($action === 'delete') {
            $res = deleteExam($req['id']);
            echo json_encode(['success' => $res]);
        } elseif ($action === 'toggle_status') {
            $statusMap = [
                'upcoming' => 'published',
                'published' => 'closed',
                'closed' => 'published'
            ];
            $newStatus = $statusMap[$req['current_status']] ?? 'upcoming';

            $res = updateExamStatus($req['id'], $newStatus);
            echo json_encode(['success' => $res, 'new_status' => $newStatus]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Action không hợp lệ']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Lỗi DB: ' . $e->getMessage()]);
    }
    exit;
}
$examsList = getExams(null, null, 1000, 0);
$examsJson = json_encode($examsList);
$classes = getClassesForDropdown();
require_once ROOT_PATH . '/app/views/pages/admin/exam-management.php';
