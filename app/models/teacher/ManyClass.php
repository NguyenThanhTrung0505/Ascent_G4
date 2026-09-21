<?php

function getAllClass($teacherId)
{
    global $db;
    $sql = "
        SELECT 
            c.id,
            c.class_code,
            c.name,
            c.icon,
            c.subject_id,
            s.name AS subject_name,
            COALESCE(st.total_students, 0) AS total_students
        FROM classes c
        INNER JOIN subjects s ON c.subject_id = s.id
        LEFT JOIN (
            SELECT class_id, COUNT(user_id) AS total_students
            FROM class_students
            GROUP BY class_id
        ) st ON c.id = st.class_id
        WHERE c.teacher_id = ?
        ORDER BY c.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function createClass($subjectName, $teacherId, $name, $icon)
{
    global $db;
    $subjectName = trim($subjectName);
    $name = trim($name);

    $db->begin_transaction();
    try {
        $stmtSub = $db->prepare("SELECT id FROM subjects WHERE name = ? LIMIT 1");
        $stmtSub->bind_param("s", $subjectName);
        $stmtSub->execute();
        $resSub = $stmtSub->get_result();

        if ($row = $resSub->fetch_assoc()) {
            $subjectId = $row['id'];
        } else {
            $stmtIns = $db->prepare("INSERT INTO subjects (name) VALUES (?)");
            $stmtIns->bind_param("s", $subjectName);
            $stmtIns->execute();
            $subjectId = $stmtIns->insert_id;
            $stmtIns->close();
        }
        $stmtSub->close();
        $classCode = generateUniqueClassCode(6);
        $sql = "INSERT INTO classes (subject_id, teacher_id, class_code, name, icon) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iisss", $subjectId, $teacherId, $classCode, $name, $icon);
        $stmt->execute();
        $classId = $stmt->insert_id;
        $stmt->close();

        $db->commit();
        return $classId;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function updateClass($classId, $className, $subjectName, $icon = null)
{
    global $db;
    $className   = trim($className);
    $subjectName = trim($subjectName);
    $classId     = (int)$classId;

    $db->begin_transaction();
    try {
        $stmtSub = $db->prepare("SELECT id FROM subjects WHERE name = ? LIMIT 1");
        $stmtSub->bind_param("s", $subjectName);
        $stmtSub->execute();
        $result = $stmtSub->get_result();

        if ($row = $result->fetch_assoc()) {
            $subjectId = $row['id'];
        } else {
            $insertSub = $db->prepare("INSERT INTO subjects (name) VALUES (?)");
            $insertSub->bind_param("s", $subjectName);
            $insertSub->execute();
            $subjectId = $insertSub->insert_id;
            $insertSub->close();
        }
        $stmtSub->close();
        $sql = "UPDATE classes SET name = ?, subject_id = ?, icon = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sisi", $className, $subjectId, $icon, $classId);
        $stmt->execute();
        $stmt->close();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function deleteClass($classId)
{
    global $db;
    $stmt = $db->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->bind_param("i", $classId);
    return $stmt->execute();
}

function generateUniqueClassCode($length = 6)
{
    global $db;
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $maxTries = 10;

    for ($i = 0; $i < $maxTries; $i++) {
        $code = '';
        for ($j = 0; $j < $length; $j++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        $stmt = $db->prepare("SELECT id FROM classes WHERE class_code = ? LIMIT 1");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $stmt->close();
            return $code;
        }
        $stmt->close();
    }
    return strtoupper(substr(bin2hex(random_bytes(4)), 0, $length));
}
