<?php
function getExams($status = null, $search = null, $limit = 10, $offset = 0)
{
    global $db;
    $sql = "SELECT e.id, e.name, e.title, e.duration, e.attempts, e.start_time, e.end_time, e.status,
                   s.name AS subject_name, c.name AS class_name, u.username AS creator_name,
                   COUNT(q.id) AS total_questions
            FROM exams e
            JOIN classes c ON e.class_id = c.id
            JOIN subjects s ON c.subject_id = s.id
            JOIN users u ON e.created_by = u.id
            LEFT JOIN questions q ON e.id = q.exam_id
            WHERE 1=1";

    $params = [];
    $types = "";

    if (!empty($status) && $status !== 'all') {
        $sql .= " AND e.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    if (!empty($search)) {
        $sql .= " AND (e.name LIKE ? OR c.name LIKE ? OR u.username LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "sss";
    }

    $sql .= " GROUP BY e.id ORDER BY e.id DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $db->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function addExam($classId, $createdBy, $name, $title, $duration, $attempts, $startTime, $endTime, $status)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO exams (class_id, created_by, name, title, duration, attempts, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissiisss", $classId, $createdBy, $name, $title, $duration, $attempts, $startTime, $endTime, $status);
    return $stmt->execute();
}

function updateExam($examId, $classId, $name, $title, $duration, $attempts, $startTime, $endTime, $status)
{
    global $db;
    $stmt = $db->prepare("UPDATE exams SET class_id = ?, name = ?, title = ?, duration = ?, attempts = ?, start_time = ?, end_time = ?, status = ? WHERE id = ?");
    $stmt->bind_param("issiisssi", $classId, $name, $title, $duration, $attempts, $startTime, $endTime, $status, $examId);
    return $stmt->execute();
}

function updateExamStatus($examId, $status)
{
    global $db;
    $stmt = $db->prepare("UPDATE exams SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $examId);
    return $stmt->execute();
}

function deleteExam($examId)
{
    global $db;
    $stmt = $db->prepare("DELETE FROM exams WHERE id = ?");
    $stmt->bind_param("i", $examId);
    return $stmt->execute();
}

function getClassesForDropdown()
{
    global $db;
    $sql = "SELECT c.id AS class_id, CONCAT(c.name, ' (', s.name, ')') AS class_display_name
            FROM classes c
            JOIN subjects s ON c.subject_id = s.id
            ORDER BY c.name ASC";

    $result = $db->query($sql);
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}
