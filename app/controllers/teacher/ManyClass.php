<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/teacher/ManyClass.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header("Location: index.php?page=auth&action=login");
    exit;
}

$teacherId = $_SESSION['user_id'];
$teacherName = $_SESSION['name'] ?? 'Giáo viên';

function getColorByIcon($icon)
{
    $iconMap = [
        '📊' => 'cyan',
        '🔢' => 'orange',
        '📐' => 'green',
        '📈' => 'orange',
        '🔺' => 'orange',
        '✖️' => 'orange',
        '📝' => 'cyan',
        '📁' => 'orange',
        '🔬' => 'cyan',
        '🌍' => 'cyan',
        '💡' => 'cyan',
        '🎯' => 'green',
        '⭐' => 'green',
        '🏆' => 'green',
        '📋' => 'green',
        '✏️' => 'green'
    ];
    return $iconMap[$icon] ?? 'orange';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['api'] ?? $_GET['action'] ?? '';
    if (ob_get_length()) {
        ob_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    if ($action === 'create-class') {
        $tenLop      = trim($_POST['ten_lop'] ?? '');
        $subjectName = trim($_POST['ten_mon_hoc'] ?? '');
        $icon        = trim($_POST['icon'] ?? '📊');

        if (empty($tenLop)) {
            echo json_encode(['success' => false, 'message' => 'Tên lớp không được để trống.']);
            exit;
        }
        $newId = createClass($subjectName, $teacherId, $tenLop, $icon);
        if ($newId !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tạo lớp thất bại, vui lòng thử lại.']);
        }
        exit;
    }

    if ($action === 'update-class') {
        $classId     = $_GET['id'] ?? null;
        $className   = trim($_POST['ten_lop'] ?? '');
        $subjectName = trim($_POST['ten_mon_hoc'] ?? '');
        $icon        = trim($_POST['icon'] ?? '📊');

        if (!$classId || empty($className)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            exit;
        }

        $isUpdated = updateClass($classId, $className, $subjectName, $icon);
        if ($isUpdated) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại.']);
        }
        exit;
    }

    if ($action === 'delete-class') {
        $classId = $_GET['id'] ?? null;
        if ($classId) {
            $isDeleted = deleteClass($classId);
            echo json_encode(['success' => $isDeleted]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    exit;
}

$stats = getAllClass($teacherId);
$danh_sach_lop_hoc = [];
foreach ($stats as $item) {
    $danh_sach_lop_hoc[] = [
        'id'            => $item['id'],
        'ma_lop'        => $item['class_code'],
        'ten_lop'       => $item['name'],
        'ten_mon_hoc'   => $item['subject_name'],
        'so_hoc_sinh'   => $item['total_students'],
        'mau_chu_dao'   => getColorByIcon($item['icon']),
        'icon_mon_hoc'  => $item['icon'],
        'is_favorite'   => false,
        'link_chi_tiet' => 'index.php?page=teacher&action=class-detail&id=' . $item['id'],
    ];
}

require_once ROOT_PATH . '/app/views/pages/teacher/many-class-teacher.php';
