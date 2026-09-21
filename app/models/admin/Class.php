<?php

function getListClasses($subjectId, $search, $limit, $offset)
{
    global $db;
    $searchWildcard = $search ? "%{$search}%" : null;
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
            u.email AS teacher_email,
            COUNT(cs.user_id) AS total_students,
            DATE_FORMAT(c.created_at, '%d/%m/%Y %H:%i') AS created_at_formatted
        FROM classes c
        JOIN subjects s ON c.subject_id = s.id
        JOIN users u ON c.teacher_id = u.id
        LEFT JOIN class_students cs ON c.id = cs.class_id
        WHERE 
            (? IS NULL OR c.subject_id = ?)
            AND (
                ? IS NULL 
                OR c.name LIKE ?
                OR c.class_code LIKE ?
                OR u.username LIKE ?
            )
        GROUP BY 
            c.id, c.class_code, c.name, c.icon, 
            s.id, s.name, 
            u.id, u.username, u.email, 
            c.created_at
        ORDER BY c.id DESC
        LIMIT ? OFFSET ?;
    ");
    $stmt->bind_param(
        "iissssii",
        $subjectId,
        $subjectId,
        $searchWildcard,
        $searchWildcard,
        $searchWildcard,
        $searchWildcard,
        $limit,
        $offset
    );
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getSubjects()
{
    global $db;
    $result = $db->query("SELECT id, name FROM subjects ORDER BY name ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTeachers()
{
    global $db;
    $result = $db->query("SELECT id, username, email FROM users WHERE role = 'teacher' AND status = 'active' ORDER BY username ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addClass($subjectId, $teacherId, $classCode, $className, $icon = null)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO classes (subject_id, teacher_id, class_code, name, icon) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $subjectId, $teacherId, $classCode, $className, $icon);
    return $stmt->execute();
}

function updateClass($classId, $subjectId, $teacherId, $className, $icon = null)
{
    global $db;
    $stmt = $db->prepare("UPDATE classes SET subject_id = ?, teacher_id = ?, name = ?, icon = ? WHERE id = ?");
    $stmt->bind_param("iissi", $subjectId, $teacherId, $className, $icon, $classId);
    return $stmt->execute();
}

function deleteClass($classId)
{
    global $db;
    $stmt = $db->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->bind_param("i", $classId);
    return $stmt->execute();
}
