<?php
require_once ROOT_PATH . '/app/models/admin/Users.php';

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
            $passwordHash = password_hash('123456', PASSWORD_DEFAULT);
            $res = addUser($req['name'], $passwordHash, $req['email'], $req['role']);
            echo json_encode(['success' => $res]);
        } elseif ($action === 'edit') {
            $res = updateUser($req['id'], $req['name'], $req['email']);
            echo json_encode(['success' => $res]);
        } elseif ($action === 'delete') {
            $res = deleteUser($req['id'], $req['role'], $adminId);
            echo json_encode(['success' => $res]);
        } elseif ($action === 'toggle_status') {
            $newStatus = ($req['current_status'] === 'active') ? 'locked' : 'active';
            $res = updateStatus($req['id'], $newStatus);
            echo json_encode(['success' => $res]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Action không hợp lệ']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Lỗi DB: ' . $e->getMessage()]);
    }
    exit;
}


$offset = 0;

$limitTeacher = getLimit('teacher', null, null);
$teachersDB = getListUser('teacher', null, null, $limitTeacher, $offset);
$formattedTeachers = [];

if (!empty($teachersDB)) {
    foreach ($teachersDB as $t) {
        $formattedTeachers[] = [
            'id'     => $t['id'],
            'name'   => $t['username'],
            'email'  => $t['email'],
            'extra'  => date('d/m/Y', strtotime($t['created_at'])),
            'status' => $t['status']
        ];
    }
}

$limitStudent = getLimit('student', null, null);
$studentsDB = getListUser('student', null, null, $limitStudent, $offset);
$formattedStudents = [];

if (!empty($studentsDB)) {
    foreach ($studentsDB as $s) {
        $formattedStudents[] = [
            'id'     => $s['id'],
            'name'   => $s['username'],
            'email'  => $s['email'],
            'extra'  => date('d/m/Y', strtotime($s['created_at'])),
            'status' => $s['status']
        ];
    }
}

$data = [
    "teacher" => [
        "addLabel"   => "+ Thêm giáo viên",
        "modalTitle" => "Thêm giáo viên mới",
        "columns"    => ["STT", "Họ và tên", "Email", "Ngày tạo", "Trạng thái", "Hành động"],
        "rows"       => $formattedTeachers
    ],
    "student" => [
        "addLabel"   => "+ Thêm học sinh",
        "modalTitle" => "Thêm học sinh mới",
        "columns"    => ["STT", "Họ và tên", "Email", "Ngày tạo", "Trạng thái", "Hành động"],
        "rows"       => $formattedStudents
    ]
];

$dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);
require_once ROOT_PATH . '/app/views/pages/admin/user-management.php';
