<?php
function getAllClassByStudentId($studentId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            c.id AS class_id,
            c.class_code,
            c.name AS class_name,      
            c.icon AS class_icon,
            s.id AS subject_id,
            s.name AS subject_name,
            u.id AS teacher_id,
            u.username AS teacher_name,
            cs.joined_at
        FROM class_students cs
        JOIN classes c ON cs.class_id = c.id
        JOIN subjects s ON c.subject_id = s.id
        JOIN users u ON c.teacher_id = u.id
        WHERE cs.user_id = ?
        ORDER BY c.name ASC
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllClassSearch($studentId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            c.id AS class_id,
            c.class_code,
            c.name AS class_name,        
            c.icon AS class_icon,
            s.id AS subject_id,
            s.name AS subject_name,         
            u.id AS teacher_id,
            u.username AS teacher_name,      
            cs.joined_at,                 
            c.created_at AS class_created_at  
        FROM class_students cs
        JOIN classes c ON cs.class_id = c.id
        JOIN subjects s ON c.subject_id = s.id
        JOIN users u ON c.teacher_id = u.id
        WHERE cs.user_id = ?
        AND (
            ? IS NULL 
            OR ? = '' 
            OR c.name LIKE CONCAT('%', ?, '%')
            OR c.class_code LIKE CONCAT('%', ?, '%')
            OR s.name LIKE CONCAT('%', ?, '%')
        )
        ORDER BY
            CASE WHEN ? = 'name' AND ? = 'asc' THEN c.name END ASC,
            CASE WHEN ? = 'name' AND ? = 'desc' THEN c.name END DESC,
            CASE WHEN ? = 'date' AND ? = 'asc' THEN cs.joined_at END ASC;
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllClassByName($studentId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            c.id AS class_id,
            c.class_code,
            c.name AS class_name,
            c.icon AS class_icon,
            s.name AS subject_name,
            u.username AS teacher_name,
            cs.joined_at
        FROM class_students cs
        JOIN classes c ON cs.class_id = c.id
        JOIN subjects s ON c.subject_id = s.id
        JOIN users u ON c.teacher_id = u.id
        WHERE cs.user_id = ?
        AND (c.name LIKE CONCAT('%', ?, '%') OR s.name LIKE CONCAT('%', ?, '%'))
        ORDER BY c.name ASC;
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllClassByDay($studentId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            c.id AS class_id,
            c.class_code,
            c.name AS class_name,
            c.icon AS class_icon,
            s.name AS subject_name,
            u.username AS teacher_name,
            cs.joined_at
        FROM class_students cs
        JOIN classes c ON cs.class_id = c.id
        JOIN subjects s ON c.subject_id = s.id
        JOIN users u ON c.teacher_id = u.id
        WHERE cs.user_id = ?
        AND (c.name LIKE CONCAT('%', ?, '%') OR s.name LIKE CONCAT('%', ?, '%'))
        ORDER BY cs.joined_at DESC;
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function joinClassByCode($studentId, $classCode)
{
    global $db;

    $stmtCheck = $db->prepare("SELECT id FROM classes WHERE class_code = ?");
    $stmtCheck->bind_param("s", $classCode);
    $stmtCheck->execute();
    $resultClass = $stmtCheck->get_result();

    if ($resultClass->num_rows === 0) {
        return ['success' => false, 'message' => 'Mã lớp không tồn tại. Vui lòng kiểm tra lại!'];
    }

    $classId = $resultClass->fetch_assoc()['id'];

    $stmtJoined = $db->prepare("SELECT user_id FROM class_students WHERE user_id = ? AND class_id = ?");
    $stmtJoined->bind_param("ii", $studentId, $classId);
    $stmtJoined->execute();

    if ($stmtJoined->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Bạn đã tham gia lớp học này từ trước rồi.'];
    }

    $stmtInsert = $db->prepare("INSERT INTO class_students (user_id, class_id) VALUES (?, ?)");
    $stmtInsert->bind_param("ii", $studentId, $classId);

    if ($stmtInsert->execute()) {
        return ['success' => true];
    }

    return ['success' => false, 'message' => 'Lỗi hệ thống CSDL khi tham gia lớp.'];
}
