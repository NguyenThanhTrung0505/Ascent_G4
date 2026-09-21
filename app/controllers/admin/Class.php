<?php
require_once ROOT_PATH . '/app/models/admin/Class.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php?page=auth&action=login");
    exit;
}
$adminId = $_SESSION['user_id'];
$adminName = $_SESSION['name'] ?? 'Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $req = json_decode(file_get_contents("php://input"), true);
    $action = $req['action'] ?? '';
    if (ob_get_length()) ob_clean();
    try {
        if ($action === 'add') {
            $res = addClass($req['subject_id'], $req['teacher_id'], $req['class_code'], $req['name']);
            echo json_encode(['success' => $res]);
        } elseif ($action === 'edit') {
            $res = updateClass($req['id'], $req['subject_id'], $req['teacher_id'], $req['name']);
            echo json_encode(['success' => $res]);
        } elseif ($action === 'delete') {
            $res = deleteClass($req['id']);
            echo json_encode(['success' => $res]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Action không hợp lệ']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()]);
    }
    exit;
}

$subjectsDB = getSubjects();
$teachersDB = getTeachers();

$classesDB = getListClasses(null, null, 1000, 0);
$formattedClasses = [];

if (!empty($classesDB)) {
    foreach ($classesDB as $c) {
        $formattedClasses[] = [
            'id'           => $c['class_id'],
            'class_code'   => $c['class_code'],
            'name'         => $c['class_name'],
            'subject_id'   => $c['subject_id'],
            'subject_name' => $c['subject_name'],
            'teacher_id'   => $c['teacher_id'],
            'teacher_name' => $c['teacher_name'],
            'size'         => $c['total_students'],
            'date'         => $c['created_at_formatted']
        ];
    }
}

$classesJson  = json_encode($formattedClasses, JSON_UNESCAPED_UNICODE);
$subjectsJson = json_encode($subjectsDB, JSON_UNESCAPED_UNICODE);
$teachersJson = json_encode($teachersDB, JSON_UNESCAPED_UNICODE);

require_once ROOT_PATH . '/app/views/pages/admin/class-management.php';
